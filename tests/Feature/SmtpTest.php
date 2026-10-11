<?php

namespace Tests\Feature;

use App\Mail\BulletinPaieMail;
use App\Mail\CongeDecisionMail;
use App\Models\BulletinPaie;
use App\Models\Conge;
use App\Models\Employe;
use App\Models\Parametre;
use App\Models\User;
use App\Services\MailConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpTest extends TestCase
{
    use RefreshDatabase;

    private const MOT_DE_PASSE = 'Mdp-SMTP-Tres-Secret-42';

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function activerSmtp(array $surcharge = []): Parametre
    {
        $param = Parametre::instance();
        $param->update(array_merge([
            'mail_actif' => true,
            'mail_host' => '127.0.0.1',
            'mail_port' => 1, // port injoignable : aucun e-mail reel n'est envoye
            'mail_encryption' => null,
            'mail_username' => 'rh@test.sn',
            'mail_password' => self::MOT_DE_PASSE,
            'mail_from_address' => 'rh@test.sn',
            'mail_from_name' => 'RH Test',
            'mail_notif_conges' => true,
        ], $surcharge));
        MailConfig::refresh();

        return $param;
    }

    private function employe(?string $email = 'awa@test.sn'): Employe
    {
        return Employe::forceCreate([
            'matricule' => 'M' . random_int(1000, 9999),
            'prenom' => 'Awa',
            'nom' => 'Diop',
            'email' => $email,
            'date_embauche' => '2020-01-01',
            'salaire_base' => 300000,
            'statut' => 'actif',
        ]);
    }

    private function bulletin(Employe $employe, string $period = '2026-09'): BulletinPaie
    {
        return BulletinPaie::create([
            'numero' => 'BP-' . $employe->id . '-' . $period,
            'employee_id' => $employe->id,
            'period' => $period,
            'salaire_base' => 300000, 'primes' => 0, 'heures_sup' => 0, 'indemnites' => 0,
            'salaire_brut' => 300000, 'cotisations_sociales' => 20000, 'impots' => 10000,
            'salaire_net' => 270000, 'statut' => 'paye',
        ]);
    }

    // ---- Page Parametres ----

    public function test_page_parametres_affiche_smtp_et_file_sans_le_mot_de_passe(): void
    {
        $this->activerSmtp();

        $this->actingAs($this->admin())->get(route('parametres.index'))
            ->assertOk()
            ->assertSee('Messagerie (SMTP)')
            ->assertSee('File d attente des e-mails')
            ->assertSee('Tester la configuration SMTP')
            ->assertDontSee(self::MOT_DE_PASSE);
    }

    public function test_employe_ne_peut_pas_acceder_aux_parametres_smtp(): void
    {
        $employe = User::factory()->create(['role' => 'employe']);

        $this->actingAs($employe)->get(route('parametres.index'))->assertRedirect();
        $this->actingAs($employe)->post(route('parametres.test-smtp'), ['destinataire' => 'x@test.sn'])->assertRedirect();
        $this->assertSame(0, DB::table('jobs')->count());
    }

    // ---- Protection des identifiants ----

    public function test_mot_de_passe_chiffre_en_base_et_conserve_si_champ_vide(): void
    {
        $param = $this->activerSmtp();

        $brut = DB::table('parametres')->where('id', $param->id)->value('mail_password');
        $this->assertNotSame(self::MOT_DE_PASSE, $brut);
        $this->assertStringNotContainsString(self::MOT_DE_PASSE, $brut);
        $this->assertSame(self::MOT_DE_PASSE, $param->fresh()->mail_password);
        $this->assertArrayNotHasKey('mail_password', $param->fresh()->toArray());

        // Enregistrement du formulaire avec un mot de passe vide : l'ancien est conserve
        $donnees = collect($param->fresh()->getAttributes())->except(['id', 'created_at', 'updated_at', 'mail_password'])->all();
        $donnees['mail_password'] = '';
        $this->actingAs($this->admin())->post(route('parametres.update'), $donnees)->assertSessionHasNoErrors();

        $this->assertSame(self::MOT_DE_PASSE, $param->fresh()->mail_password);
    }

    public function test_mot_de_passe_jamais_remis_en_session_apres_erreur_de_validation(): void
    {
        $this->actingAs($this->admin())
            ->from(route('parametres.index'))
            ->post(route('parametres.update'), ['mail_password' => self::MOT_DE_PASSE, 'mail_actif' => 1])
            ->assertSessionHasErrors('nom_entreprise');

        $this->assertNull(session()->getOldInput('mail_password'));
        $this->assertStringNotContainsString(self::MOT_DE_PASSE, json_encode(session()->all()));
    }

    // ---- E-mail de test et echec d'envoi ----

    public function test_email_de_test_refuse_si_smtp_non_configure(): void
    {
        config(['mail.default' => 'array']);

        $this->actingAs($this->admin())->from(route('parametres.index'))
            ->post(route('parametres.test-smtp'), ['destinataire' => 'x@test.sn'])
            ->assertSessionHas('error');
    }

    public function test_echec_smtp_affiche_une_erreur_sans_exposer_le_mot_de_passe(): void
    {
        $this->activerSmtp();

        $reponse = $this->actingAs($this->admin())->from(route('parametres.index'))
            ->post(route('parametres.test-smtp'), ['destinataire' => 'x@test.sn']);

        $reponse->assertRedirect(route('parametres.index'))->assertSessionHas('error');
        $this->assertStringContainsString('Echec', session('error'));
        $this->assertStringNotContainsString(self::MOT_DE_PASSE, session('error'));
    }

    public function test_job_en_echec_visible_dans_parametres_et_relancable(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => 'test-uuid-1', 'connection' => 'database', 'queue' => 'default',
            'payload' => json_encode(['uuid' => 'test-uuid-1', 'displayName' => BulletinPaieMail::class, 'job' => 'x', 'data' => []]),
            'exception' => "Symfony\\Component\\Mailer\\Exception\\TransportException: Connection refused\n#0 trace",
            'failed_at' => now(),
        ]);
        config(['queue.default' => 'database']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('parametres.index'))
            ->assertSee('1 en echec')
            ->assertSee('Relancer les echecs')
            ->assertSee('Connection refused')
            ->assertDontSee('#0 trace');

        $this->actingAs($admin)->post(route('parametres.relancer-emails'))->assertSessionHas('success');

        $this->assertSame(0, DB::table('failed_jobs')->count());
        $this->assertSame(1, DB::table('jobs')->count());
    }

    // ---- File d'attente ----

    public function test_decision_de_conge_mise_en_file_pour_l_employe(): void
    {
        $this->activerSmtp();
        Mail::fake();
        $employe = $this->employe();
        $conge = Conge::create([
            'employee_id' => $employe->id, 'type' => 'maladie', 'date_debut' => '2026-10-12',
            'date_fin' => '2026-10-14', 'jours' => 3, 'statut' => 'en_attente',
        ]);

        $this->actingAs($this->admin())->post(route('conges.rejeter', $conge))->assertSessionHas('success');

        Mail::assertQueued(CongeDecisionMail::class, fn ($m) => $m->hasTo('awa@test.sn') && $m->conge->is($conge));
        Mail::assertNothingSent();
    }

    public function test_pas_de_notification_conge_si_option_desactivee(): void
    {
        $this->activerSmtp(['mail_notif_conges' => false]);
        Mail::fake();
        $conge = Conge::create([
            'employee_id' => $this->employe()->id, 'type' => 'maladie', 'date_debut' => '2026-10-12',
            'date_fin' => '2026-10-14', 'jours' => 3, 'statut' => 'en_attente',
        ]);

        $this->actingAs($this->admin())->post(route('conges.rejeter', $conge));

        Mail::assertNothingOutgoing();
        $this->assertSame('refuse', $conge->fresh()->statut);
    }

    public function test_bulletins_de_la_periode_mis_en_file(): void
    {
        $this->activerSmtp();
        Mail::fake();
        $this->bulletin($this->employe('a@test.sn'));
        $this->bulletin($this->employe('b@test.sn'));
        $this->bulletin($this->employe(null));

        $this->actingAs($this->admin())->post(route('bulletins.envoyer-tous'), ['period' => '2026-09'])
            ->assertSessionHas('success', fn ($msg) => str_contains($msg, '2 bulletin(s)') && str_contains($msg, '1 employe(s) sans adresse'));

        Mail::assertQueued(BulletinPaieMail::class, 2);
    }

    public function test_job_en_file_ne_contient_pas_les_identifiants_smtp(): void
    {
        $this->activerSmtp();
        config(['queue.default' => 'database']);
        $bulletin = $this->bulletin($this->employe());

        $this->actingAs($this->admin())->post(route('bulletins.envoyer', $bulletin))->assertSessionHas('success');

        $payload = DB::table('jobs')->value('payload');
        $this->assertNotNull($payload);
        $this->assertStringContainsString('BulletinPaieMail', $payload);
        $this->assertStringNotContainsString(self::MOT_DE_PASSE, $payload);
    }

    public function test_worker_recharge_la_config_smtp_avant_chaque_job(): void
    {
        $param = $this->activerSmtp(['mail_host' => 'ancien.smtp.test']);
        $this->assertSame('ancien.smtp.test', config('mail.mailers.smtp.host'));

        // Modification faite depuis l'interface pendant que le worker tourne
        $param->update(['mail_host' => 'nouveau.smtp.test']);
        \Illuminate\Support\Facades\Cache::forget(MailConfig::CACHE_KEY);
        event(new \Illuminate\Queue\Events\JobProcessing('database', \Mockery::mock(\Illuminate\Contracts\Queue\Job::class)->shouldIgnoreMissing()));

        $this->assertSame('nouveau.smtp.test', config('mail.mailers.smtp.host'));
    }

    // ---- Piece jointe PDF ----

    public function test_bulletin_envoye_avec_pdf_en_piece_jointe(): void
    {
        $bulletin = $this->bulletin($this->employe());
        $mail = new BulletinPaieMail($bulletin);

        $mail->assertHasSubject('Votre bulletin de paie - 2026-09');
        $mail->assertSeeInHtml('270 000');

        $pieces = $mail->attachments();
        $this->assertCount(1, $pieces);
        $contenu = $pieces[0]->attachWith(fn () => null, fn ($donnees, $piece) => [$donnees(), $piece->as, $piece->mime]);

        [$pdf, $nom, $mime] = $contenu;
        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertSame('bulletin-' . $bulletin->numero . '.pdf', $nom);
        $this->assertSame('application/pdf', $mime);
    }
}

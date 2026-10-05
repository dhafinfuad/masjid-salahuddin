<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\ProgramParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;
    protected User $jamaah;
    protected FinanceCategory $categoryInfaq;
    protected FinanceCategory $categoryOperasional;
    protected Agenda $agendaRamadhan;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat default Category untuk event dashboard
        Category::create([
            'name' => 'Kajian Rutin',
            'slug' => 'kajian',
            'color_badge' => 'emerald',
            'description' => 'Kajian rutin pekanan',
        ]);

        $this->bendahara = User::create([
            'name' => 'H. Mukhlis Syarif',
            'email' => 'bendahara@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Bendahara',
            'status' => 'AKTIF',
        ]);

        $this->jamaah = User::create([
            'name' => 'Haji Mansyur',
            'email' => 'jamaah@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        $this->categoryInfaq = FinanceCategory::create([
            'name' => 'Kotak Infaq Jumat',
            'type' => 'pemasukan',
            'group' => 'penerimaan',
            'color' => 'emerald',
        ]);

        $this->categoryOperasional = FinanceCategory::create([
            'name' => 'Honorarium Khotib',
            'type' => 'pengeluaran',
            'group' => 'pengeluaran_rutin',
            'color' => 'rose',
        ]);

        $this->agendaRamadhan = Agenda::create([
            'title' => 'Semarak Ramadhan 1447 H',
            'description' => 'Kegiatan Takjil & Iktikaf',
            'event_date' => Carbon::now()->addDays(5),
            'budget' => 45000000,
            'status' => 'Berjalan',
        ]);
    }

    public function test_officer_can_view_finance_tab_with_metrics(): void
    {
        $this->actingAs($this->bendahara);

        Finance::create([
            'transaction_date' => Carbon::today(),
            'type' => 'pemasukan',
            'category_id' => $this->categoryInfaq->id,
            'program_name' => 'Kas Umum',
            'amount' => 5000000,
            'description' => 'Infaq Kotak Utama Jumat',
            'recorded_by' => $this->bendahara->id,
        ]);

        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->assertOk()
            ->assertSee('Aliran Kas Utama')
            ->assertSee('Kategori')
            ->assertSee('Infaq Kotak Utama Jumat')
            ->assertSee('5.000.000');
    }

    public function test_officer_can_create_edit_and_delete_finance_category_with_groups(): void
    {
        $this->actingAs($this->bendahara);

        // 1. Create
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('newCategoryName', 'Santunan Sosial')
            ->set('newCategoryGroup', 'pengeluaran_nonrutin')
            ->set('newCategoryColor', 'purple')
            ->call('saveFinanceCategory');

        $this->assertDatabaseHas('finance_categories', [
            'name' => 'Santunan Sosial',
            'group' => 'pengeluaran_nonrutin',
            'type' => 'pengeluaran',
        ]);

        $cat = FinanceCategory::where('name', 'Santunan Sosial')->first();

        // 2. Edit
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('editFinanceCategory', $cat->id)
            ->set('newCategoryName', 'Bakti Sosial & Santunan')
            ->set('newCategoryGroup', 'pengeluaran_rutin')
            ->call('saveFinanceCategory');

        $this->assertDatabaseHas('finance_categories', [
            'id' => $cat->id,
            'name' => 'Bakti Sosial & Santunan',
            'group' => 'pengeluaran_rutin',
        ]);

        // 3. Delete
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('deleteFinanceCategory', $cat->id);

        $this->assertDatabaseMissing('finance_categories', [
            'id' => $cat->id,
        ]);
    }

    public function test_officer_can_record_pemasukan_and_pengeluaran_with_agenda(): void
    {
        $this->actingAs($this->bendahara);

        // Catat Pemasukan Kas Umum
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeType', 'pemasukan')
            ->set('financeCategoryId', $this->categoryInfaq->id)
            ->set('financeAgendaId', null)
            ->set('financeProgramName', 'Kas Umum')
            ->set('financeAmount', '3500000')
            ->set('financeDate', Carbon::today()->format('Y-m-d'))
            ->set('financeDescription', 'Kotak Infaq Sayap Kanan')
            ->call('saveFinance');

        $this->assertDatabaseHas('finances', [
            'type' => 'pemasukan',
            'amount' => 3500000,
            'description' => 'Kotak Infaq Sayap Kanan',
            'agenda_id' => null,
        ]);

        // Catat Pengeluaran Terkait Agenda Kegiatan
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeType', 'pengeluaran')
            ->set('financeCategoryId', $this->categoryOperasional->id)
            ->set('financeAgendaId', $this->agendaRamadhan->id)
            ->set('financeAmount', '1200000')
            ->set('financeDate', Carbon::today()->format('Y-m-d'))
            ->set('financeDescription', 'Konsumsi Buka Puasa Bersama')
            ->call('saveFinance');

        $this->assertDatabaseHas('finances', [
            'type' => 'pengeluaran',
            'amount' => 1200000,
            'agenda_id' => $this->agendaRamadhan->id,
            'description' => 'Konsumsi Buka Puasa Bersama',
        ]);
    }

    public function test_officer_can_edit_and_delete_finance_transaction(): void
    {
        $this->actingAs($this->bendahara);

        $fin = Finance::create([
            'transaction_date' => Carbon::today(),
            'type' => 'pemasukan',
            'category_id' => $this->categoryInfaq->id,
            'program_name' => 'Kas Umum',
            'amount' => 1000000,
            'description' => 'Sedekah Subuh',
            'recorded_by' => $this->bendahara->id,
        ]);

        // Edit
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('editFinance', $fin->id)
            ->set('financeAmount', '1500000')
            ->set('financeDescription', 'Sedekah Subuh & Dhuha')
            ->call('saveFinance');

        $this->assertDatabaseHas('finances', [
            'id' => $fin->id,
            'amount' => 1500000,
            'description' => 'Sedekah Subuh & Dhuha',
        ]);

        // Delete
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('deleteFinance', $fin->id);

        $this->assertDatabaseMissing('finances', [
            'id' => $fin->id,
        ]);
    }

    public function test_officer_can_register_and_delete_program_participant(): void
    {
        $this->actingAs($this->bendahara);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('participantName', 'Ahmad Farhan, S.E.')
            ->set('participantProgram', 'Santunan Anak Yatim')
            ->set('participantAmount', '250000')
            ->set('participantPeriod', 'Bulanan')
            ->call('saveParticipant');

        $this->assertDatabaseHas('program_participants', [
            'name' => 'Ahmad Farhan, S.E.',
            'program_name' => 'Santunan Anak Yatim',
            'monthly_amount' => 250000,
        ]);

        $part = ProgramParticipant::where('name', 'Ahmad Farhan, S.E.')->first();

        // Delete Participant
        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->call('deleteParticipant', $part->id);

        $this->assertDatabaseMissing('program_participants', [
            'id' => $part->id,
        ]);
    }

    public function test_officer_can_record_lump_sum_deposit(): void
    {
        $this->actingAs($this->bendahara);

        Livewire::test(AdminDashboard::class, ['tab' => 'programs'])
            ->set('lumpSumAmount', '12500000')
            ->set('lumpSumDate', Carbon::today()->format('Y-m-d'))
            ->set('lumpSumProgram', 'Infaq Rutin Pegawai')
            ->set('lumpSumNotes', 'Transfer Kliring Kolektif Bendahara Gaji KPP')
            ->call('processLumpSumDeposit');

        $this->assertDatabaseHas('finances', [
            'type' => 'pemasukan',
            'program_name' => 'Infaq Rutin Pegawai',
            'amount' => 12500000,
        ]);
    }

    public function test_read_only_user_cannot_save_finance(): void
    {
        $this->actingAs($this->jamaah);

        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeType', 'pemasukan')
            ->set('financeCategoryId', $this->categoryInfaq->id)
            ->set('financeAmount', '999999')
            ->set('financeDate', Carbon::today()->format('Y-m-d'))
            ->set('financeDescription', 'Upaya Ilegal Jamaah')
            ->call('saveFinance');

        $this->assertDatabaseMissing('finances', [
            'description' => 'Upaya Ilegal Jamaah',
        ]);
    }

    public function test_export_pdf_monthly_and_agenda_lpj_modes(): void
    {
        $ketua = User::create([
            'name' => 'Nazil Fuadi',
            'email' => 'ketua@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Ketua',
            'status' => 'AKTIF',
        ]);

        // Tambah 2 transaksi pengeluaran rutin dengan pos anggaran yang sama
        Finance::create([
            'user_id' => $this->bendahara->id,
            'category_id' => $this->categoryOperasional->id,
            'type' => 'pengeluaran',
            'amount' => 400000,
            'transaction_date' => Carbon::now(),
            'description' => 'Khotib pekan 1',
        ]);
        Finance::create([
            'user_id' => $this->bendahara->id,
            'category_id' => $this->categoryOperasional->id,
            'type' => 'pengeluaran',
            'amount' => 400000,
            'transaction_date' => Carbon::now(),
            'description' => 'Khotib pekan 2',
        ]);

        $this->actingAs($this->bendahara);

        // 1. Monthly routine PDF
        $monthlyPdf = $this->get(route('admin.finance.export-pdf', ['type' => 'monthly', 'month' => date('n'), 'year' => date('Y'), 'tte' => 1]));
        $monthlyPdf->assertOk();
        $monthlyPdf->assertSee('LAPORAN ARUS KAS', false);
        $monthlyPdf->assertSee('Saldo Awal Bulan', false);
        $monthlyPdf->assertSee('TTE BSrE DISAHKAN', false);
        // Pastikan kolom keterangan tidak ada
        $monthlyPdf->assertDontSee('KETERANGAN / URAIAN TRANSAKSI', false);
        // Pastikan nama pengurus sesuai data users
        $monthlyPdf->assertSee('Nazil Fuadi', false);
        $monthlyPdf->assertSee('H. Mukhlis Syarif', false);
        // Pastikan pos anggaran yang sama ter-sum (400.000 + 400.000 = 800.000)
        $monthlyPdf->assertSee('Honorarium Khotib', false);
        $monthlyPdf->assertSee('800.000', false);

        // 2. Agenda Thematic LPJ PDF
        $agendaPdf = $this->get(route('admin.finance.export-pdf', ['type' => 'agenda', 'agenda_id' => $this->agendaRamadhan->id, 'tte' => 1]));
        $agendaPdf->assertOk();
        $agendaPdf->assertSee('LAPORAN PERTANGGUNGJAWABAN (LPJ) KEUANGAN KEGIATAN', false);
        $agendaPdf->assertSee(strtoupper($this->agendaRamadhan->title), false);
        $agendaPdf->assertSee('Sisa Anggaran (Silpa)', false);
        $agendaPdf->assertSee('Nazil Fuadi', false);

        // 3. Excel export
        $excelResponse = $this->get(route('admin.finance.export-excel'));
        $excelResponse->assertOk();
        $excelResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_subtab_navigation_bar_contains_kelola_kategori_kas_and_removes_old_cards(): void
    {
        $this->actingAs($this->bendahara);

        $response = $this->get('/admin?tab=finance');
        $response->assertOk();

        // Sub-tab button Kelola Kategori Kas must be present
        $response->assertSee('Kelola Kategori Kas');
        $response->assertSee('Daftar Pos Anggaran');
        $response->assertSee('Tambah Pos Anggaran');

        // The old cards (Grafik Arus Kas & Visual Bar Chart) must be removed
        $response->assertDontSee('Grafik Arus Kas');
        $response->assertDontSee('Komparasi total kas masuk vs kas keluar');
        $response->assertDontSee('Distribusi Kategori Kas');
    }

    public function test_admin_can_manage_finance_categories_in_page(): void
    {
        $this->actingAs($this->bendahara);

        // 1. Create new category
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('newCategoryName', 'Honorarium Khotib Baru')
            ->set('newCategoryGroup', 'pengeluaran_rutin')
            ->call('saveFinanceCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('finance_categories', [
            'name' => 'Honorarium Khotib Baru',
            'group' => 'pengeluaran_rutin',
            'type' => 'pengeluaran',
        ]);

        $createdCat = FinanceCategory::where('name', 'Honorarium Khotib Baru')->first();

        // 2. Edit category
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('editFinanceCategory', $createdCat->id)
            ->assertSet('isEditingFinanceCategory', true)
            ->assertSet('editingFinanceCategoryId', $createdCat->id)
            ->assertSet('newCategoryName', 'Honorarium Khotib Baru')
            ->set('newCategoryName', 'Honorarium Khotib Updated')
            ->call('saveFinanceCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('finance_categories', [
            'id' => $createdCat->id,
            'name' => 'Honorarium Khotib Updated',
        ]);

        // 3. Delete category
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('deleteFinanceCategory', $createdCat->id);

        $this->assertDatabaseMissing('finance_categories', [
            'id' => $createdCat->id,
        ]);
    }

    public function test_finance_category_modal_and_subtab_lifecycle(): void
    {
        $this->actingAs($this->bendahara);

        // Test openFinanceCategoryModal dispatches open event and sets showFinanceCategoryModal
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeSubTab', 'kategori')
            ->call('openFinanceCategoryModal')
            ->assertSet('showFinanceCategoryModal', true)
            ->assertDispatched('open-finance-category-modal')
            ->assertSet('financeSubTab', 'kategori');

        // Test editFinanceCategory dispatches open event and retains subtab
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeSubTab', 'kategori')
            ->call('editFinanceCategory', $this->categoryInfaq->id)
            ->assertSet('showFinanceCategoryModal', true)
            ->assertDispatched('open-finance-category-modal')
            ->assertSet('financeSubTab', 'kategori');

        // Test saveFinanceCategory dispatches close event and retains subtab
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeSubTab', 'kategori')
            ->set('newCategoryName', 'Pos Baru Test Event')
            ->set('newCategoryGroup', 'penerimaan')
            ->call('saveFinanceCategory')
            ->assertSet('showFinanceCategoryModal', false)
            ->assertDispatched('close-finance-category-modal')
            ->assertSet('financeSubTab', 'kategori');
    }

    public function test_finance_modal_category_dropdown_filters_by_finance_type(): void
    {
        $this->actingAs($this->bendahara);

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('openFinanceModal')
            ->assertSet('financeType', 'pemasukan')
            ->assertSet('financeCategoryId', $this->categoryInfaq->id)
            ->assertSee('📥 PENERIMAAN')
            ->assertSeeHtml('<option value="' . $this->categoryInfaq->id . '">' . $this->categoryInfaq->name . '</option>')
            ->assertDontSee('📤 PENGELUARAN RUTIN')
            ->assertDontSeeHtml('<option value="' . $this->categoryOperasional->id . '">' . $this->categoryOperasional->name . '</option>');

        // Switch to pengeluaran
        $component->call('setFinanceType', 'pengeluaran')
            ->assertSet('financeType', 'pengeluaran')
            ->assertSet('financeCategoryId', $this->categoryOperasional->id)
            ->assertSee('📤 PENGELUARAN RUTIN')
            ->assertSeeHtml('<option value="' . $this->categoryOperasional->id . '">' . $this->categoryOperasional->name . '</option>')
            ->assertDontSee('📥 PENERIMAAN')
            ->assertDontSeeHtml('<option value="' . $this->categoryInfaq->id . '">' . $this->categoryInfaq->name . '</option>');

        // Switch back to pemasukan
        $component->call('setFinanceType', 'pemasukan')
            ->assertSet('financeType', 'pemasukan')
            ->assertSet('financeCategoryId', $this->categoryInfaq->id)
            ->assertSee('📥 PENERIMAAN')
            ->assertSeeHtml('<option value="' . $this->categoryInfaq->id . '">' . $this->categoryInfaq->name . '</option>')
            ->assertDontSee('📤 PENGELUARAN RUTIN')
            ->assertDontSeeHtml('<option value="' . $this->categoryOperasional->id . '">' . $this->categoryOperasional->name . '</option>');
    }

    public function test_admin_can_filter_finance_transactions_by_multiple_months_simultaneously(): void
    {
        $this->actingAs($this->bendahara);

        Finance::create([
            'transaction_date' => Carbon::create(2026, 1, 15),
            'type' => 'pemasukan',
            'category_id' => $this->categoryInfaq->id,
            'program_name' => 'Kas Umum',
            'amount' => 1000000,
            'description' => 'Infaq Transaksi Januari',
            'recorded_by' => $this->bendahara->id,
        ]);

        Finance::create([
            'transaction_date' => Carbon::create(2026, 3, 20),
            'type' => 'pemasukan',
            'category_id' => $this->categoryInfaq->id,
            'program_name' => 'Kas Umum',
            'amount' => 3000000,
            'description' => 'Infaq Transaksi Maret',
            'recorded_by' => $this->bendahara->id,
        ]);

        Finance::create([
            'transaction_date' => Carbon::create(2026, 7, 10),
            'type' => 'pemasukan',
            'category_id' => $this->categoryInfaq->id,
            'program_name' => 'Kas Umum',
            'amount' => 7000000,
            'description' => 'Infaq Transaksi Juli',
            'recorded_by' => $this->bendahara->id,
        ]);

        // Filter: Semua Bulan (empty array or 'all')
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeYearFilter', 'all')
            ->set('financeMonthFilter', [])
            ->assertSee('Infaq Transaksi Januari')
            ->assertSee('Infaq Transaksi Maret')
            ->assertSee('Infaq Transaksi Juli');

        // Filter: Hanya Januari dan Maret
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeYearFilter', 'all')
            ->set('financeMonthFilter', ['1', '3'])
            ->assertSee('Infaq Transaksi Januari')
            ->assertSee('Infaq Transaksi Maret')
            ->assertDontSee('Infaq Transaksi Juli');

        // Filter: Hanya Juli
        Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeYearFilter', 'all')
            ->set('financeMonthFilter', ['7'])
            ->assertDontSee('Infaq Transaksi Januari')
            ->assertDontSee('Infaq Transaksi Maret')
            ->assertSee('Infaq Transaksi Juli');
    }

    public function test_toggle_all_and_reset_finance_months(): void
    {
        $this->actingAs($this->bendahara);

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->set('financeMonthFilter', ['1'])
            ->call('toggleAllFinanceMonths')
            ->assertSet('financeMonthFilter', array_map('strval', range(1, 12)))
            ->call('toggleAllFinanceMonths')
            ->assertSet('financeMonthFilter', [])
            ->set('financeMonthFilter', ['2', '4'])
            ->call('resetFinanceMonths')
            ->assertSet('financeMonthFilter', []);
    }

    public function test_tte_signature_persists_per_period_and_updates_on_filter_change(): void
    {
        $this->actingAs($this->bendahara);

        // Bersihkan storage signatures test
        \App\Services\TteSignatureService::unsign('monthly-2026-1');
        \App\Services\TteSignatureService::unsign('monthly-2026-2');

        $component = Livewire::test(AdminDashboard::class, ['tab' => 'finance'])
            ->call('openPrintFinanceModal')
            ->set('printReportType', 'monthly')
            ->set('printYear', '2026')
            ->set('printMonth', '1')
            ->assertSet('tteSigned', false);

        // Sahkan TTE untuk Januari 2026
        $component->call('signTteReport')
            ->assertSet('tteSigned', true);

        // Ganti bulan ke Februari 2026 -> TTE harus belum disahkan (false)
        $component->set('printMonth', '2')
            ->assertSet('tteSigned', false);

        // Kembalikan ke Januari 2026 -> TTE harus tetap tersimpan tervalidasi (true)
        $component->set('printMonth', '1')
            ->assertSet('tteSigned', true);

        // Cek bahwa saat Januari 2026 diekspor, TTE BSrE terdeteksi
        $responseJan = $this->get('/admin/finance/export-pdf?type=monthly&month=1&year=2026');
        $responseJan->assertStatus(200);
        $responseJan->assertSee('TTE BSrE DISAHKAN');

        // Cek bahwa Februari 2026 yang belum disahkan (tte=0) tidak menampilkan TTE
        $responseFeb = $this->get('/admin/finance/export-pdf?type=monthly&month=2&year=2026&tte=0');
        $responseFeb->assertStatus(200);
        $responseFeb->assertDontSee('TTE BSrE DISAHKAN');

        // Bersihkan kembali setelah test
        \App\Services\TteSignatureService::unsign('monthly-2026-1');
        \App\Services\TteSignatureService::unsign('monthly-2026-2');
    }
}



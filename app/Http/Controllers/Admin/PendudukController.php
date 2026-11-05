<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PendudukController extends Controller
{
    public const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
    public const KAWIN = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
    public const PENDIDIKAN = ['Tidak/Belum Sekolah', 'SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];

    public function index(Request $r)
    {
        $q = Penduduk::orderBy('nama');
        if ($c = trim((string) $r->query('q'))) {
            $q->where(fn ($w) => $w->where('nama', 'like', "%{$c}%")->orWhere('nik', 'like', "%{$c}%")->orWhere('no_kk', 'like', "%{$c}%"));
        }
        foreach (['rw', 'rt', 'status', 'jenis_kelamin'] as $f) {
            if ($v = $r->query($f)) {
                $q->where($f, $v);
            }
        }
        return view('admin.penduduk.index', [
            'daftar' => $q->paginate(20)->withQueryString(),
            'rw' => Penduduk::distinct()->orderBy('rw')->pluck('rw'),
            'ringkas' => ['total' => Penduduk::where('status', 'aktif')->count(), 'l' => Penduduk::where(['status' => 'aktif', 'jenis_kelamin' => 'L'])->count(), 'p' => Penduduk::where(['status' => 'aktif', 'jenis_kelamin' => 'P'])->count(), 'kk' => Penduduk::where('status', 'aktif')->distinct('no_kk')->count('no_kk')],
        ]);
    }

    private function aturan(?Penduduk $p = null): array
    {
        return [
            'nik' => ['required', 'digits:16', Rule::unique('penduduk', 'nik')->ignore($p?->id)], 'no_kk' => 'required|digits:16',
            'nama' => 'required|string|max:120', 'tempat_lahir' => 'required|string|max:80', 'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|in:L,P', 'agama' => 'required|in:' . implode(',', self::AGAMA), 'status_perkawinan' => 'required|in:' . implode(',', self::KAWIN),
            'pekerjaan' => 'nullable|string|max:80', 'pendidikan' => 'nullable|in:' . implode(',', self::PENDIDIKAN),
            'alamat' => 'required|string|max:200', 'rt' => 'required|digits_between:1,3', 'rw' => 'required|digits_between:1,3',
            'kewarganegaraan' => 'required|in:WNI,WNA', 'status' => 'required|in:aktif,pindah,meninggal',
        ];
    }

    private function rapikan(array $d): array
    {
        $d['rt'] = str_pad($d['rt'], 3, '0', STR_PAD_LEFT);
        $d['rw'] = str_pad($d['rw'], 3, '0', STR_PAD_LEFT);
        $d['nama'] = mb_strtoupper(trim($d['nama']));
        return $d;
    }

    public function create()
    {
        return view('admin.penduduk.form', ['p' => new Penduduk(['kewarganegaraan' => 'WNI', 'status' => 'aktif'])]);
    }

    public function store(Request $r)
    {
        $p = Penduduk::create($this->rapikan($r->validate($this->aturan(), [], ['nik' => 'NIK', 'no_kk' => 'nomor KK'])));
        return redirect()->route('admin.penduduk.show', $p)->with('ok', 'Data penduduk ditambahkan.');
    }

    public function show(Penduduk $penduduk)
    {
        return view('admin.penduduk.show', ['p' => $penduduk, 'keluarga' => Penduduk::where('no_kk', $penduduk->no_kk)->where('id', '!=', $penduduk->id)->orderBy('tanggal_lahir')->get(), 'riwayat' => $penduduk->permohonan()->with('layanan')->latest()->get()]);
    }

    public function edit(Penduduk $penduduk)
    {
        return view('admin.penduduk.form', ['p' => $penduduk]);
    }

    public function update(Request $r, Penduduk $penduduk)
    {
        $penduduk->update($this->rapikan($r->validate($this->aturan($penduduk), [], ['nik' => 'NIK', 'no_kk' => 'nomor KK'])));
        return redirect()->route('admin.penduduk.show', $penduduk)->with('ok', 'Data penduduk diperbarui.');
    }

    public function destroy(Penduduk $penduduk)
    {
        if ($penduduk->permohonan()->exists()) {
            return back()->with('error', 'Penduduk ini punya riwayat permohonan. Ubah statusnya menjadi pindah/meninggal, jangan dihapus.');
        }
        $penduduk->delete();
        return redirect()->route('admin.penduduk.index')->with('ok', 'Data penduduk dihapus.');
    }

    public function template()
    {
        $isi = "nik;no_kk;nama;tempat_lahir;tanggal_lahir;jenis_kelamin;agama;status_perkawinan;pekerjaan;pendidikan;alamat;rt;rw\n"
            . "3273010101900001;3273010101900099;BUDI SANTOSO;Bandung;1990-01-01;L;Islam;Kawin;Wiraswasta;SMA/SMK;Jl. Melati No. 5;001;002\n";
        return response($isi, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="template-penduduk.csv"']);
    }

    /** Impor CSV (pemisah ; atau ,). NIK yang sudah ada diperbarui. */
    public function impor(Request $r)
    {
        $r->validate(['berkas' => 'required|file|mimes:csv,txt|max:4096'], [], ['berkas' => 'file CSV']);
        $h = fopen($r->file('berkas')->getRealPath(), 'r');
        $baris1 = fgets($h);
        $pisah = substr_count($baris1, ';') >= substr_count($baris1, ',') ? ';' : ',';
        $kolom = array_map(fn ($k) => strtolower(trim($k, " \t\n\r\0\x0B\xEF\xBB\xBF\"")), str_getcsv($baris1, $pisah, '"', ''));
        $baru = $ubah = 0;
        $galat = [];
        $n = 1;
        while (($row = fgetcsv($h, 0, $pisah, '"', '')) !== false) {
            $n++;
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            $d = array_combine($kolom, array_pad(array_slice($row, 0, count($kolom)), count($kolom), ''));
            $d = array_map(fn ($v) => trim((string) $v), $d) + ['kewarganegaraan' => 'WNI', 'status' => 'aktif'];
            $d['jenis_kelamin'] = strtoupper(substr($d['jenis_kelamin'] ?? '', 0, 1));
            if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $d['tanggal_lahir'] ?? '', $m)) {
                $d['tanggal_lahir'] = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }
            $ada = Penduduk::where('nik', $d['nik'] ?? '')->first();
            $v = validator($d, $this->aturan($ada));
            if ($v->fails()) {
                $galat[] = "Baris {$n}: " . $v->errors()->first();
                continue;
            }
            $data = $this->rapikan(array_intersect_key($v->validated(), $this->aturan()));
            $ada ? $ada->update($data) : Penduduk::create($data);
            $ada ? $ubah++ : $baru++;
        }
        fclose($h);
        return back()->with('ok', "Impor selesai: {$baru} baru, {$ubah} diperbarui.")->with('galat_impor', array_slice($galat, 0, 20));
    }
}

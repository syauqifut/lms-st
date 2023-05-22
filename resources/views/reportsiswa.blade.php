<!DOCTYPE html>
<html>
<head>
<style>
    tr.b th, tr.b td {
        border: 1px solid black;
    }
    tr.t td{
        border-top: 1px solid black;
    }
</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div width="100%">

<table  width="100%" style="font-size: 15px;">
    <tr>
        <th rowspan="3"><img src="{{ base_path() }}/public/images/blacklogo.png" style='width:65px' alt="Logo"></th>
        <th colspan='11'> SEKOLAH TINGGI AGAMA ISLAM</th>
    </tr>
    <tr>
        <th colspan='11'> S T A I &nbsp; A L &nbsp; F I T H R A H</th>
    </tr>
    <tr>
        <th colspan='11'>  Jl. Kedinding Lor 30 Surabaya 60129 Telp. 03137301276 Email : stai.alfithrah@yahoo.com</th>
    </tr>
    <tr style="text-align:center; " class="t">
        <td colspan="12" ><h3>KARTU HASIL STUDI</h3></td>
    </tr>
    <tr>
        <td>Nama</td>
        <td>: {{ $nama }}</td>
        <td></td>
        <td></td>
        <td colspan="3">Kelas</td>
        <td colspan="5">: {{ $kelas }}</td>
    </tr>
    <tr>
        <td>NIM</td>
        <td>: {{ $nim }}</td>
        <td></td>
        <td></td>
        <td colspan="3">Tahun Pelajaran/semester</td>
        <td colspan="5">: {{ $tahun }}/{{ $semester }}</td>
    </tr>
    <tr style="text-align:center" class="b">
        <th width="40">KODE</th>
        <th width="235">MATA KULIAH</th>
        <th width="150">DOSEN</th>
        <th width="40">TUGAS</th>
        <th width="40">UTS</th>
        <th width="40">UAS</th>
        <th width="40">PERFORM</th>
        <th width="15">S</th>
        <th width="15">I</th>
        <th width="15">A</th>
        <th width="65">ANGKA</th>
        <th width="65">HURUF</th>
    </tr>
    @php $i=1 @endphp
    @foreach($rapor as $list)
    <tr class="b">
        <td>{{$list->subject_code }}</td>
        <td>{{$list->subject}}</td>
        <td>{{$list->gurupengajar}}</td>
        <td style="text-align:center">{{$list->tugas}}</td>
        <td style="text-align:center">{{$list->uts}}</td>
        <td style="text-align:center">{{$list->uas}}</td>
        <td style="text-align:center">{{$list->perform}}</td>
        <td style="text-align:center">{{$list->sakit}}</td>
        <td style="text-align:center">{{$list->izin}}</td>
        <td style="text-align:center">{{$list->alpha}}</td>
        <td style="text-align:center">{{$list->nilai}}</td>
        <td style="text-align:center">{{$list->huruf}}</td>
    </tr>
    @endforeach
    <tr style="text-align:center;" >
        <td colspan='12' style="height:150px" ></td>
    </tr>
</table>
<!-- FOOTER -->
<table  width="100%" style="font-size: 15px;">
    <tr style="text-align:center">
        <td style="width:75%"></td>
        <td style="width:25%">Surabaya, {{$tanggal}}</td>
    </tr>
    <tr style="text-align:center">
        <td style="width:75%"></td>
        <td style="width:25%">A.n. Ketua,</td>
    </tr>
    <tr style="text-align:center">
        <td style="width:75%"></td>
        <td style="width:25%">Kaprodi {{ $kategori }}</td>
    </tr>
    <tr style="text-align:center" >
        <td colspan='2' style="height:100px" ></td>
    </tr>
    <tr style="text-align:center">
        <td style="width:75%"></td>
        <td style="width:25%">.....................................................</td>
    </tr> 
</table>

</div>
</body>
</html>

<table>
    <thead>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <th colspan="12" style="text-align: center;"><b>DAFTAR HADIR SISWA</b></th>
        </tr>
        <tr>
            <th></th>
            <th colspan="12" style="text-align: center;"><b>SEKOLAH TINGGI X</b></th>
        </tr>
        <tr>
            <th></th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th><b>JUR/PRODI : {{ $group->classes }}</b></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><b>MATA KULIAH : {{ $subject->name }}</b></th>
        </tr>
        <tr>
            <th></th>
            <th><b>SEMESTER : 
                @if($group->academicterms=='1')
                    GANJIL
                @else
                    GENAP
                @endif     
            </b></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><b>TH. AKADEMIK : {{ $group->year }}</b></th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="2" width="5"><b>No.</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="2" width="20"><b>NIM</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="2" width="30"><b>NAMA MAHASISWA</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="2" width="6"><b>L/P</b></th>
            <?php $tgl = 0;?>
            @foreach($tanggal as $tanggalspan)
                <?php $tgl++ ?> 
            @endforeach
            <th style="border: 1px solid black; text-align: center;" colspan="<?php echo $tgl ?>"><b>TANGGAL</b></th>
            <th style="border: 1px solid black; text-align: center;" colspan="4"><b>TOTAL</b></th>
        </tr>
        <tr>
            <th></th>
            @foreach($tanggal as $tanggal)
                <th style="border: 1px solid black; text-align: center;"><b> {{ date('d M', strtotime($tanggal['date'])) }} </b></th>
            @endforeach
            <th style="border: 1px solid black; text-align: center;" width="6"><b>H</b></th>
            <th style="border: 1px solid black; text-align: center;" width="6"><b>I</b></th>
            <th style="border: 1px solid black; text-align: center;" width="6"><b>S</b></th>
            <th style="border: 1px solid black; text-align: center;" width="6"><b>A</b></th>
        </tr>
    </thead>
    <tbody>
    <?php $number = 1 ?>
    @foreach($rekaps as $siswa)
        <tr>
            <td></td>
            <td style="border: 1px solid black;"> {{ $number }} </td>
            <td style="border: 1px solid black;"> {{ $siswa['nim'] }} </td>
            <td style="border: 1px solid black;"> {{ $siswa['nama'] }} </td>
            <td style="border: 1px solid black;"> {{ $siswa['gender'] }} </td>
            <?php $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0; $i = 0;?>
            @foreach($siswa['siswarekap'] as $status)
                <td style="border: 1px solid black;"> {{ $status['status'] }} </td>
                @if ( $status['status'] == 'P')
                    <?php $hadir++ ?>    
                @elseif ( $status['status'] == 'L')
                    <?php $izin++ ?> 
                @elseif ( $status['status'] == 'S')
                    <?php $sakit++ ?> 
                @elseif ( $status['status'] == 'A')
                    <?php $alpha++ ?> 
                @endif
                <?php $i++ ?> 
            @endforeach
            <?php $span = 0 ?>
            @if($i != $tgl)
                <?php $span = $tgl - $i ?>
            @endif
            @if($span != 0)
                <td style="border: 1px solid black;" colspan="<?php echo $span ?>"></td>
            @endif
            
            <td style="border: 1px solid black;"> {{ $hadir }} </td>
            <td style="border: 1px solid black;"> {{ $izin }} </td>
            <td style="border: 1px solid black;"> {{ $sakit }} </td>
            <td style="border: 1px solid black;"> {{ $alpha }} </td>
        </tr>
        <?php $number++ ?>
    @endforeach
    </tbody>
</table>
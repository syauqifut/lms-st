<table>
    <thead>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <th colspan="12" style="text-align: center;"><b>REKAP KEHADIRAN KBM  ASATIDZ/H</b></th>
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
            <th><b>PERIODE : {{ $tglmulai }} s.d {{ $tglakhir }}</b></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="3" width="5"><b>No.</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="3" width="15"><b>NIY</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" rowspan="3" width="30"><b>NAMA</b></th>
            <?php $tgl = 0;?>
            @foreach($tanggal as $tanggalspan)
                <?php $tgl++ ?> 
            @endforeach
            <th style="border: 1px solid black; text-align: center;" colspan="<?php echo $tgl*5 ?>"><b>TANGGAL</b></th>
            <th style="border: 1px solid black; text-align: center;" colspan="5"><b>TOTAL</b></th>
        </tr>
        <tr>
            <th></th>
            @foreach($tanggal as $tanggal)
                <th style="border: 1px solid black; text-align: center;" colspan="5"><b> {{ date('l, d M', strtotime($tanggal['date'])) }} </b></th>
            @endforeach
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" width="4" rowspan="2"><b>H</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" width="4" rowspan="2"><b>I</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" width="4" rowspan="2"><b>S</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" width="4" rowspan="2"><b>A</b></th>
            <th style="border: 1px solid black; text-align: center; vertical-align: center;" width="4" rowspan="2"><b>N</b></th>
        </tr>
        <tr>
            <th></th>
            <?php for ($des = 0; $des < $tgl; $des++) { 
                echo"
                <th style='border: 1px solid black; text-align: center;'><b> H </b></th>
                <th style='border: 1px solid black; text-align: center;'><b> I </b></th>
                <th style='border: 1px solid black; text-align: center;'><b> S </b></th>
                <th style='border: 1px solid black; text-align: center;'><b> A </b></th>
                <th style='border: 1px solid black; text-align: center;'><b> N </b></th>
                ";
            } ?>
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
            <?php $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0; $none = 0; $i = 0;?>
            @foreach($tanggalsiswa as $tanggalsiswaa)
                <?php $tanggalan = $tanggalsiswaa['datereal'] ?>
                <!-- <td style="border: 1px solid black;"> {{ $tanggalan }} </td> -->
                <td style="border: 1px solid black; background-color:#AEABAB" width="5">
                    @foreach($siswa['siswarekap'] as $statushadir)
                        @if($statushadir['date'] == $tanggalan)
                            @if($statushadir['hadir'] != 0)
                                {{ $statushadir['hadir'] }}
                                <?php $hadir += $statushadir['hadir'] ?>
                            @endif
                        @endif
                        
                    @endforeach
                </td>
                <td style="border: 1px solid black; background-color:#FFFFFF" width="5">
                    @foreach($siswa['siswarekap'] as $statusizin)
                        @if($statusizin['date'] == $tanggalan)
                            @if($statusizin['izin'] != 0)
                                {{ $statusizin['izin'] }}
                                <?php $izin += $statusizin['izin'] ?>
                            @endif
                        @endif
                    @endforeach
                </td>
                <td style="border: 1px solid black; background-color:#FFE598" width="5">
                    @foreach($siswa['siswarekap'] as $statussakit)
                        @if($statussakit['date'] == $tanggalan)
                            @if($statussakit['sakit'] != 0)
                                {{ $statussakit['sakit'] }}
                                <?php $sakit += $statussakit['sakit'] ?>
                            @endif
                        @endif
                    @endforeach
                </td>
                <td style="border: 1px solid black; background-color:#A8D08D" width="5">
                    @foreach($siswa['siswarekap'] as $statusalpha)
                        @if($statusalpha['date'] == $tanggalan)
                            @if($statusalpha['alpha'] != 0)
                                {{ $statusalpha['alpha'] }}
                                <?php $alpha += $statusalpha['alpha'] ?>
                            @endif
                        @endif
                    @endforeach
                </td>
                <td style="border: 1px solid black; background-color:#F56C68" width="5">
                    @foreach($siswa['siswarekap'] as $statusnone)
                        @if($statusnone['date'] == $tanggalan)
                            @if($statusnone['none'] != 0)
                                {{ $statusnone['none'] }}
                                <?php $none += $statusnone['none'] ?>
                            @endif
                        @endif
                    @endforeach
                </td>

            @endforeach
            <td style="border: 1px solid black;"> {{ $hadir }} </td>
            <td style="border: 1px solid black;"> {{ $izin }} </td>
            <td style="border: 1px solid black;"> {{ $sakit }} </td>
            <td style="border: 1px solid black;"> {{ $alpha }} </td>
            <td style="border: 1px solid black;"> {{ $none }} </td>
        </tr>
        <?php $number++ ?>
    @endforeach
    </tbody>
</table>
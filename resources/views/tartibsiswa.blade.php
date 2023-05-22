<table>
    <thead>
        <tr>
            <th colspan="4"><img src="{{ base_path() }}/public/images/tartiblogo.png" style="display:block;" width="100%" height="100%" alt="Logo"></th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th>NIM</th>
            <th colspan="2">: {{ $user->username }}</th>
        </tr>
        <tr>
            <th></th>
            <th>Nama</th>
            <th colspan="2">: {{ $user->fullname }}</th>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th colspan="4"><b>Perilaku Positif</b></th>
        </tr>
        <tr>
            <th colspan="3">Poin</th>
            <th>{{ $sumpoinpositif }}</th>
        </tr>
        <tr>
            <th >No</th>
            <th >Group</th>
            <th >Attitude</th>
            <th >Poin</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tartibpositif as $tartibpositif)
        <tr>
            <td>{{ $tartibpositif->id }}</td>
            <td>{{ $tartibpositif->classes }}</td>
            <td>{{ $tartibpositif->nama_pelanggaran }}</td>
            <td>{{ $tartibpositif->poin }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th colspan="4"><b>Perilaku Negatif</b></th>
        </tr>
        <tr>
            <th colspan="3">Poin</th>
            <th>{{ $sumpoinnegatif }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Group</th>
            <th>Attitude</th>
            <th>Poin</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tartibnegatif as $tartibnegatif)
        <tr>
            <td>{{ $tartibnegatif->id }}</td>
            <td>{{ $tartibnegatif->classes }}</td>
            <td>{{ $tartibnegatif->nama_pelanggaran }}</td>
            <td>{{ $tartibnegatif->poin }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload</title>
</head>
<body>
    <center>
        <form action="{{ route('upload.csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            
            <input type="file" name="upload" id="">
            <br>
            <br>
            <br>
            <br>
            <button>Upload</button>
        </form>
    </center>
    <br>
    <br>
    @if (session()->has('errors'))
        <h3>Failed Uploads</h3>
        <br>
        <table>
            @foreach (session('errors') as $item)
                {{ implode(' || ', $item) }}
                <br>
                <br>
            @endforeach
        </table>
    @endif
</body>
</html>
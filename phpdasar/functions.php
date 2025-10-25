<?php
// koneksi ke database
$conn = mysqli_connect("localhost","root", "", "phpdasar");

// fungsi query biar mudah di file lain
function query($query) {
	global $conn; //ambil variable conn dari luar fungsi
	$result = mysqli_query($conn, $query);
	$rows = [];
	while($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows; //hasilnya array berisi semua data
}

function tambah($data) {
    global $conn;

    // Ambil data dari tiap elemen form
    $nama = htmlspecialchars($data["nama"]);
    $nim = htmlspecialchars($data["nim"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
	
	// upload gambar
	$gambar = upload();
	if (!$gambar) {
		return false;
	}

    // Query insert data
    $query = "INSERT INTO mahasiswa
              VALUES ('', '$nama', '$nim', '$email', '$jurusan', '$gambar')";

    mysqli_query($conn, $query);

    // Cek apakah data berhasil ditambahkan
    return mysqli_affected_rows($conn);
}

// ===== Fungsi Upload Gambar =====
function upload() {
	$namaFile = $_FILES['gambar']['name'];
	$ukuranFile = $_FILES['gambar']['size'];
	$error = $_FILES['gambar']['error'];
	$tmpName = $_FILES['gambar']['tmp_name'];

	// Cek apakah tidak ada gambar yang diupload
	if ($error === 4) {
		echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
		return false;
	}

	// Cek ekstensi file
	$ekstensiValid = ['jpg', 'jpeg', 'png'];
	$ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
	if (!in_array($ekstensiFile, $ekstensiValid)) {
		echo "<script>alert('Yang kamu upload bukan gambar!');</script>";
		return false;
	}

	// Cek ukuran file
	if ($ukuranFile > 1000000) {
		echo "<script>alert('Ukuran gambar terlalu besar!');</script>";
		return false;
	}

	// Generate nama baru agar unik
	$namaFileBaru = uniqid() . '.' . $ekstensiFile;

	move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

	return $namaFileBaru;
}

function hapus($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id");
    return mysqli_affected_rows($conn);
}

function ubah($data) {
    global $conn;
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $nim = htmlspecialchars($data["nim"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
$gambarLama = htmlspecialchars($data["gambarLama"]);

	// Cek apakah user pilih gambar baru atau tidak
	if ($_FILES['gambar']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		$gambar = upload();
	}

	$query = "UPDATE mahasiswa SET 
				nama = '$nama',
				nim = '$nim',
				email = '$email',
				jurusan = '$jurusan',
				gambar = '$gambar'
			  WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}
?>

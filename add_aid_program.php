<?php
$conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
if (!$conn) {
    die("Connection failed");
}

$id = $_POST['id'] ?? '';
$program_name = $_POST['program_name'];
$aid_type = $_POST['aid_type'];
$date_scheduled = $_POST['date_scheduled'];
$beneficiaries = $_POST['beneficiaries'];
$status = $_POST['status'];

if ($id == "") {
    // INSERT
    $stmt = mysqli_prepare($conn,
        "INSERT INTO aid_program 
        (program_name, aid_type, date_scheduled, beneficiaries, status)
        VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sssis",
        $program_name, $aid_type, $date_scheduled, $beneficiaries, $status
    );
} else {
    // UPDATE
    $stmt = mysqli_prepare($conn,
        "UPDATE aid_program SET
            program_name=?,
            aid_type=?,
            date_scheduled=?,
            beneficiaries=?,
            status=?
        WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "sssisi",
        $program_name, $aid_type, $date_scheduled, $beneficiaries, $status, $id
    );
}

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

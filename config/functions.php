<?php

session_start();
require_once 'dbcon.php';

//Input Field Validation Function
function validate($inputData){
    global $conn;
    $validatedData = mysqli_real_escape_string($conn,$inputData);
    return trim($validatedData);
}

//Redirect from 1 to another page 
function redirect($url,$status){
    $_SESSION['status'] = $status;
    header('Location'.$url);
    exit(0);
}

//Display message or status after any other operation
function displayMessage(){
    if(isset($_SESSION['status'])){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6>'.$_SESSION['status'].'</h6>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
        unset($_SESSION['status']);
    }
}
 //insert record using this function
 function insert($tablename, $data){
    global $conn;

    $table = validate($tablename);

    $columns =array_keys($data);
    $values  = array_values($data);

    $finalColumn = implode(',', $columns);
    $finalValues = "'".implode("','", $values)."'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    $result = mysqli_query($conn, $query);
    return $result;
 }

    //update record using this function

    function update($tablename, $id,$data){
        global $conn;

        $table = validate($tablename);
        $id = validate($id);
        $updateDataString = '';

        foreach($data as $column => $value){
            $updateDataString .= "$column.'='.'$value', ";
        }
       $finalUpdateData =substr(trim($updateDataString), 0, -1);

        $query = "UPDATE $table SET $finalUpdateData WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
 }
function getAll($tableName,$status=null){
    global $conn;

    $table = validate($tableName);
    $query = "SELECT * FROM $table";
    // If status is provided, add a WHERE clause
    if($status != null){
        $query .= " WHERE status='$status'";
    }
    $query .= " ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    return $result;
}


function getById($tableName, $id){
    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
    $result = mysqli_query($conn, $query);
        if($result){
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                $response = [
                    'status' => 200,
                    'data' => $row,
                    'message' => 'Data fetched successfully'
                ];
                return $response;
            }else{
                $response = [
                    'status' => 404,
                    'message' => 'No data found'
                ];
            }

        }else{
            $response = [
                'status' => 500,
                'message' => 'Error in fetching data: '.mysqli_error($conn)
            ];
        }

    return mysqli_fetch_array($result);
}


//delete data from database using id

function delete($tableName, $id){
    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "DELETE FROM $table WHERE id='$id'LIMIT 1";
    $result = mysqli_query($conn, $query);
    return $result;
}

?>
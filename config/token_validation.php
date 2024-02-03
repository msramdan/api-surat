<?php
function validateToken($db, $token) {
    $tokenParts = explode(" ", $token);
    if (count($tokenParts) == 2 && $tokenParts[0] == "Bearer") {
        $actualToken = $tokenParts[1];

        $query = "SELECT * FROM user WHERE token = '$actualToken' AND token_expiration > " . time();
        $result = $db->query($query);

        return $result->num_rows > 0;
    }

    return false;
}
?>

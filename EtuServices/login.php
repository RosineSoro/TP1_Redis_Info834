<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="login">Email :</label><br>
        <input type="text" id="login" name="login"><br>
        <label for="password">Mot de Passe :</label><br>
        <input type="password" id="password" name="password"><br>
        <input type="submit" value="Submit">
    </form>

    <?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $email = $_POST["login"];
    $mdp = $_POST["password"];

    // Établir une connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "rosis";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Préparer et exécuter la requête SQL pour vérifier les informations de connexion

    $sql = "SELECT id FROM utilisateurs WHERE email = '$email' AND mdp = '$mdp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Les informations de connexion sont correctes
        // Récupérer l'id_utilisateur correspondant
        $row = $result->fetch_assoc();
        $id_utilisateur = $row["id"];

        // Exécuter le script Python avec l'id_utilisateur
        $cmd = "C:\\Users\\rosys\\anaconda3\\python.exe" . " " . "C:\\Users\\rosys\\OneDrive\\Bureau\\IDU4\\S8\\INFO834\\TP1_SORO\\tp1_SORO_codePython.py" . " " . $id_utilisateur;
        //echo $cmd;
        $command = escapeshellcmd($cmd);
        $shelloutput = shell_exec($command);
        // Vérifier la sortie du script Python
        if (strpos($shelloutput, "Connexion autorisee!") !== false) {
            header('location: services.php');
        }else {
            echo "<pre class='error-message'>$shelloutput</pre>"; 
            echo "<div id='countdown'></div>"; // Afficher le compte à rebours
            ?>
            <script>
                // Définir la durée du compte à rebours en secondes (2 minutes = 120 secondes)
                const countdownDuration = 120;

                // Fonction pour mettre à jour le compte à rebours
                function updateCountdown() {
                    // Obtenir l'élément où afficher le compte à rebours
                    const countdownElement = document.getElementById('countdown');

                    // Obtenir le moment actuel
                    const currentTime = Math.floor(Date.now() / 1000);

                    // Calculer le temps écoulé depuis le début du compte à rebours
                    const elapsedTime = currentTime - startTime;

                    // Calculer le temps restant
                    const remainingTime = countdownDuration - elapsedTime;

                    // Vérifier si le temps restant est écoulé
                    if (remainingTime <= 0) {
                        // Afficher un message lorsque le temps est écoulé
                        countdownElement.innerText = 'Temps écoulé';
                        return;
                    }

                    // Convertir le temps restant en minutes et secondes
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;

                    // Afficher le compte à rebours
                    countdownElement.innerText = `Temps restant : ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                }

                // Obtenir le moment où le compte à rebours a commencé
                const startTime = Math.floor(Date.now() / 1000);

                // Mettre à jour le compte à rebours toutes les secondes
                setInterval(updateCountdown, 1000);

                // Appeler la fonction updateCountdown pour afficher le compte à rebours initial
                updateCountdown();
            </script>
            <?php
         }
    } else {
        // Afficher un message d'erreur
        echo "<span class='error-message'>Identifiants incorrects.</span>";
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>

</body>
</html>

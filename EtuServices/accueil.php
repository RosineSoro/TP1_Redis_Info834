
<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $mdp = $_POST["mdp"];
    //hashage du mot de passe avant de le stocker dans la base de donnes pour plus de sécurité.
    $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

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

    // Vérifier si l'adresse e-mail est déjà utilisée
   $sql_check_email = "SELECT id FROM utilisateurs WHERE email = '$email'";
   $result_check_email = $conn->query($sql_check_email);

  if ($result_check_email->num_rows > 0) {
    echo "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
  } else {
    // Préparer et exécuter la requête SQL pour insérer les données de l utilisateur dans la base de données
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mdp)
            VALUES ('$nom', '$prenom', '$email', '$mdp_hash')";

    if ($conn->query($sql) === TRUE) {
        echo "Enregistrement des informations d'inscription réussi.";
    } else {
        echo "Erreur lors de l'enregistrement des informations : " . $conn->error;
    }
  }


    // Fermer la connexion à la base de données
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - EtuServices</title>
    <link rel="stylesheet" href="accueil.css">
</head>
<body>
    <h1>Bienvenue sur EtuServices</h1>
    <p>Découvrez nos services pour les étudiants</p>
    
    <!-- Formulaire d'inscription -->
    <h2>Créer un compte</h2>
    <form method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br>
        <label for="mdp">Mot de passe :</label>
        <input type="password" id="mdp" name="mdp" required><br>
        <input type="submit" value="S'inscrire">
    </form>
    
    <!-- Lien vers la page de connexion -->
    <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>



</body>
</html>

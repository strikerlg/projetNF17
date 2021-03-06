<?php include("include/head.php"); ?>
<body>
	<?php include("include/menu.php"); ?>
	<div id="main">
		<h1>Ajouter un karateka</h1>
		<form method="POST" action="#">
			<table>
				<tr><td>Nom :</td><td><?php echo $_POST['nom']; ?></td></tr>
				<tr><td>Club :</td><td>
				<?php
					$query = "SELECT id,nom
								FROM club
								WHERE id=".$_POST['id_club'].";";
					$reponse = pg_query($bdd,$query);
					$data = pg_fetch_array($reponse);
					echo $data['nom'];
				?>
				</td></tr>
				<tr><td>Poids :</td><td><?php echo $_POST['poids']." kg"; ?></td></tr>
				<tr><td>Taille :</td><td><?php if(isset($_POST['taille'])){echo $_POST['taille']." cm";} else {echo "erreur taille";} ?></td></tr>
				<tr><td>Date de naissance : </td><td><?php
					if(isset($_POST['naiss_jour'])){echo $_POST['naiss_jour']." / ";} else {echo "erreur date";}
					if(isset($_POST['naiss_mois'])){echo $_POST['naiss_mois']." / ";} else {echo "erreur date";}
					if(isset($_POST['naiss_annee'])){echo $_POST['naiss_annee'];} else {echo "erreur date";}
				?></td></tr>
				<tr><td>Ceinture :</td><td><?php if(isset($_POST['ceinture'])){echo $_POST['ceinture'];} else{echo "erreur ceinture";}; ?></td></tr>
				<?php if($_POST['ceinture']=="noire"){echo "<tr><td>Dans :</td><td>".$_POST['dans']."</td></tr>";};?>
				<tr><td>Photo :</td><td><?php if($_POST['photo']!=""){echo $_POST['photo'];} else{echo "[Pas de photo disponible]";}; ?></td></tr>
				<tr><td>Katas maîtrisés :</td><td>
					<?php
						$imax=$_POST['imax'];
						for($i=1; $i<=$imax; $i++){
							if(isset($_POST[$i])){ echo $_POST[$i]."<br/>"; }
						}
					?>
				</td></tr>
			</table><br/>
			<input class="button" type="button" value="Retour" onclick="history.go(-1)"/>
		</form>
	<?php //création de la requête pour la table karateka
		$debut = "INSERT INTO karateka (id, id_club, nom, poids, taille, dateNais, photo, ceinture, dans) VALUES (";
		$id = "NEXTVAL('karateka_id_seq')";
		$nom = "'".$_POST['nom']."'";
		$club = $_POST['id_club'];
		$poids = $_POST['poids'];
		$taille = $_POST['taille'];
		$dateNais = "'".$_POST['naiss_annee']."-0".$_POST['naiss_mois']."-0".$_POST['naiss_jour']."'";
		$ceinture = "'".$_POST['ceinture']."'";
		if(isset($_POST['ceinture']) && $_POST['ceinture'] == 'noire') $dans = $_POST['dans']; else $dans = "NULL";
		if(isset($_POST['photo']) && $_POST['photo']!="") $photo = "'".$_POST['photo'].""; else $photo = "NULL";
		$fin = ");";
		
		$query = $debut.$id.",".$club.",".$nom.",".$poids.",".$taille.",".$dateNais.",".$photo.",".$ceinture.",".$dans.$fin;
		try {
    		//$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			pg_query($bdd,$query);
			echo "<br/>Le Karateka a bien été ajouté !";
	    }
		catch(PDOException $e){
	    	echo "<br/>ERREUR REQUETE : ".$query . "<br/>CODE ERREUR : " . $e->getMessage();
	    }
		
		//création des requêtes pour la table maîtrise (des katas)
		$debut = "INSERT INTO maitrise (id_karateka,id_kata) VALUES (";
			$query = "SELECT id FROM karateka WHERE nom='".$_POST['nom']."';";
			//echo $query;
			$reponse = pg_query($bdd,$query);
			$data = pg_fetch_array($reponse);
			$id_karateka = $data['id']; //récupération de l'id du karateka ajouté grâce à la requête juste précédente
		$fin = ");";
		
		$imax = $_POST['imax'];
		for($i=1;$i<=$imax; $i++){
			if(isset($_POST[$i])){ //si le kata a été précédemment coché, on ajoute son id dans la table de maîtrise
				$id_kata = $i;
				$query=$debut.$id_karateka.",".$id_kata.$fin;
				try{
					//$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					pg_query($bdd,$query);
					echo "<br/>Le Kata ".$id_kata." a bien été ajouté !";
				}
				catch(PDOException $e){
	    			echo "<br/>ERREUR REQUETE : ".$query . "<br/>CODE ERREUR : " . $e->getMessage();
	    		}
			}
		}
		?>
	</div>
</body>
<?php include("include/foot.php"); ?>
<!DOCTYPE html>
<html>
<!--
	Created by DPD- (Davide Peressoni) in 2016
	Released under GNU/GPL 2 LICENSE (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
	Fork me on: https://github.com/DPDmancul/Campo-Minato-Fiorito
-->
	<head>
		<title>Campo fiorito minato</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-fork-ribbon-css/0.2.0/gh-fork-ribbon.min.css" />
		<!--[if lt IE 9]>
    	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-fork-ribbon-css/0.2.0/gh-fork-ribbon.ie.min.css" />
		<![endif]-->
		<style>
			table{border-collapse: collapse;}
			td{width:100px;height:100px;border: 1px solid black;text-align:center;vertical-align:middle;}
			td input, td img {width:100%;height:100%;}
			.active{background-color:red;}
		</style>
	</head>
	<body>
		<a class="github-fork-ribbon" target="_blank" href="https://github.com/DPDmancul/Campo-Minato-Fiorito" title="Fork me on GitHub">Fork me on GitHub</a>
		<h1>Campo fiori e bombe</h1>
		<?php
		define('BOMBA', '10');
		define('FIORE', '1');
		define('ERBA', '0');
			$status=$open=-1;
			$end=false;
			$c[BOMBA]=$c[FIORE]=$c[ERBA]=0;
			session_start();
			if(isset($_POST['dim'])&&isset($_POST['diff'])){
				$dim=$_SESSION['dim']=$_POST['dim'];
				$diff=$_SESSION['diff']=$_POST['diff'];
				$_SESSION['game']=array(array('new'));}
			if(isset($_GET['new'])||!isset($_SESSION['dim'])){
				$_SESSION['game']=array(array('new'));
				if(!isset($_POST['dim'])||!isset($_POST['diff'])){
		?>
						<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
							Dimensione del campo: <input name="dim" min="3" type="number" value="5"> <br>
							Difficolt&agrave;: <input name="diff" type="radio" value="1"></input>1
										<input name="diff" type="radio" checked value="2"></input>2
										<input name="diff" type="radio" value="3"></input>3
										<input name="diff" type="radio" value="extreme"></input>extreme<br>
							<input type="submit" value="Crea tabellone">
						</form>
		<?php }}
			if(!isset($_GET['new'])&&isset($_SESSION['dim'])){
				$dim=$_SESSION['dim'];
				$diff=$_SESSION['diff'];
				$new=false;
				if(!isset($_SESSION['game'])||$_SESSION['game'][0][0]=='new'){	//creo array di gioco
					$new=true;
					$game=array(array());
					for($i=0;$i<$dim;$i++)
						for($j=0;$j<$dim;$j++)
							$game[$i][$j]='false';
					$_SESSION['game']=$game;
				}else
					$game=$_SESSION['game'];
				if(!isset($_SESSION['campo'])||$new){//Creo il vettore (fungente da matrice) casuale per il campo
					switch ($diff){
					case 1:
						$cb=8;
						$cf=5;
						break;
					case 2:
						$cb=6;
						$cf=6;
						break;
					case 3:
						$cb=5;
						$cf=8;
						break;
					default:
						$cb=2.5;
						$cf=10;
					}
					$bombe=floor($dim*$dim/$cb);
					$fiori=ceil($dim*$dim/$cf);
					$vet=array();
					$i=0;
					for ($j=0;$j<$bombe;$j++,$i++)
						$vet[]=BOMBA;
					for ($j=0;$j<$fiori;$j++,$i++)
						$vet[]=FIORE;
					for(;$i<$dim*$dim;$i++)
						$vet[]=ERBA;
					for ($i=0;$i<$dim*$dim;$i++)//mescolo gli elementi
						for($j=0;$j<$dim*$dim;$j++){
							$a=rand(0,$dim*$dim-1);
							$b=rand(0,$dim*$dim-1);
							$t=$vet[$a];
							$vet[$a]=$vet[$b];
							$vet[$b]=$t;
						}
					$_SESSION['campo']=$vet;
				}else{
					$vet=$_SESSION['campo'];
				}
				if(isset($_POST['open'])){
					$open=$_POST['open'];
					$j=$open%$dim;
					$i=($open-$j)/$dim;
					$game[$i][$j]='true';
					$status=$vet[$open];
					if ($status==ERBA){
						if($j>0)
							$c[$vet[$open-1]]++;
						if($j<$dim-1)
							$c[$vet[$open+1]]++;
						if($i>0)
							$c[$vet[$dim*($i-1)+$j]]++;
						if($i<$dim-1)
							$c[$vet[$dim*($i+1)+$j]]++;
						if($j>0 && $i>0)
							$c[$vet[$dim*($i-1)+($j-1)]]++;
						if($j<$dim-1  && $i<$dim-1)
							$c[$vet[$dim*($i+1)+($j+1)]]++;
						if($i>0 && $j<$dim-1)
							$c[$vet[$dim*($i-1)+($j+1)]]++;
						if($i<$dim-1 && $j>0)
							$c[$vet[$dim*($i+1)+($j-1)]]++;
						$game[$i][$j] = "${c[FIORE]} Fiori<br>${c[BOMBA]} Bombe";
					}else
						$end=true;
					$_SESSION['game']=$game;
				}
		?>
				<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
					<table>
						<?php
							for ($i=0;$i<$dim;$i++){
								echo '<tr>';
								for ($j=0;$j<$dim;$j++){
									echo ($dim*$i+$j==$open?'<td class="active">':'<td>');
									if($end||$game[$i][$j]=='true')
										echo "<img src=\"img/${vet[$dim*$i+$j]}.png\" />";
									else
										if($game[$i][$j]=='false')
											echo '<input type="submit" name="open" value="'.($dim*$i+$j).'">';
										else
											echo $game[$i][$j];
									echo '</td>';
									}
								echo '</tr>';
							}
						?>
					</table>
				</form>
		<?php
			}
			echo '<h2>';
			switch ($status){
				case ERBA:
					echo "Fiori vicini:${c[FIORE]}, bombe vicine: ${c[BOMBA]}";
					break;
				case FIORE:
					echo 'Hai vinto!';
					break;
				case BOMBA:
					echo 'Hai perso';
			}
		?>
		</h2>
		<p><a href="<?php echo $_SERVER['PHP_SELF']?>?new">Ricomincia</a></p>
	</body>
</html>
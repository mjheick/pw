<?php
function randomPassword($len = 8)
{
	$pw = '';
	$alphabet = '1234567890QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm.,?;:][{}_-=+()*&^%$#@!';
	for ($x = 0; $x < $len; $x++)
	{
		$pw = $pw . substr($alphabet, mt_rand(0, strlen($alphabet)), 1);
	}
	return $pw;
}
function sendBack($param_root) {
	header("Location: " . $param_root, 301);
	die();
}
/**
 * Variables
 */
$ROOT = ''; /* httpx://domain/stuff Where this lives */
if ($_SERVER['HTTP_HOST'] != '') { /* Insert domain name here */
	sendBack($ROOT);
}
//if ($_SERVER['SERVER_PORT'] != '443')
if (!array_key_exists('HTTP_HTTPS', $_SERVER)) {
	sendBack($ROOT);
}

$MYSQL_HOST = 'localhost';
$MYSQL_USER = '';
$MYSQL_PASS = '';
$MYSQL_DB = '';
$link = mysqli_connect($MYSQL_HOST , $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

/* password database manager */
/* check auth from form post, if good, redirect */
$word = isset($_COOKIE['access']) ? $_COOKIE['access'] : '';
if ($word == '') {
	$word = isset($_POST['access']) ? md5(trim($_POST['access'])) : '';
	if (strlen($word) == 0) {
		/**
		 * Need login screen
		 */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
	</head>
	<body>
		<center>
Enter Password :)<br>
			<form method='post' action='<?php echo $ROOT; ?>'>
				<input name='access' type='password' value='spanked' />
				<input type='submit' value='auth' />
			</form>
		</center>
	</body>
</html>
<?php
	die();
	}
}


$authlevel = "";
$query = "SELECT `id` FROM `tbl_password_users` WHERE `pass`='$word' LIMIT 1";
$res = mysqli_query($link, $query);
while ($r = mysqli_fetch_assoc($res)) {
	$authlevel = $r['id'];
}
if ($authlevel == "") {
	setcookie("access", "", 0);
	sendBack($ROOT);
}
setcookie("access", $word);
$action = isset($_GET['action']) ? trim($_GET['action']) : '';
/**
 * Non-displayable actions
 */
$sendback = false;
switch ($action) {
	case "flip":
		if ($authlevel == "0")
		{
			$id = $_GET['id'];
			$query = "UPDATE `tbl_password` SET `visible`='Y' WHERE `id`=$id LIMIT 1";
			mysqli_query($link, $query);
		}
		$sendback = true;
	break;

	case "logout":
		setcookie("access", "", 0);
		$sendback = true;
	break;

	case "kill":
		$id = $_GET['id'];
		$query = "UPDATE `tbl_password` SET `visible`='N' WHERE (`id`='$id' AND `owner`='$authlevel') LIMIT 1";
		mysqli_query($link, $query);
		$sendback = true;
	break;

	case "edit2":
		$id = $_GET['id'];
		$site = $_GET['site'];
		$user = base64_encode($_GET['user']);
		$pass = base64_encode($_GET['pass']);
		$notes = base64_encode($_GET['notes']);
		$query = "update `tbl_password` set `site`='$site', `user`='$user', `pass`='$pass', `notes`='$notes' where (`id`='$id' and `owner`='$authlevel') limit 1";
		mysqli_query($link, $query);
		$sendback = true;
	break;

	case "addnew2":
		$site = $_GET['site'];
		$user = base64_encode($_GET['user']);
		$pass = base64_encode($_GET['pass']);
		$notes = base64_encode($_GET['notes']);
		$query = "insert into `tbl_password` (`id`, `site`, `user`, `pass`, `notes`, `owner`) VALUES (NULL, '" . $site . "', '" . $user . "', '" . $pass . "', '" . $notes . "', '$authlevel')";
		mysqli_query($link, $query);
		$sendback = true;
	break;
}
if ($sendback) {
	sendBack($ROOT);
}

/**
 * Displayable actions
 */
?>
<html>
	<head>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style>
table {
	border-collapse: collapse;
}
table, td {
	border: 1px solid rgb(123, 123, 123);
}
td, input {
	padding: 2px 4px;
}
input, button {
	width: 250px;
}
input:hover {
	background-color: yellow;
}
a {
	font-weight: bold;
}
a:hover {
	background-color: yellow;
	color: black;
}
.comment {
	background-color: rgb(234, 234, 234);
}
.url {
	max-width: 500px;
	overflow: none;
}
		</style>
		<script>
function flipButton2Text(id)
{
	// since we need to be security-focused-minded, if there are any other
	// "showing" buttons, we need to hide them
	var buttonNodeList = document.getElementsByClassName("form-button");
	var inputNodeList = document.getElementsByClassName("form-input");

	for (var x = 0; x < inputNodeList.length; x++)
	{
		var node = inputNodeList[x].getAttribute("id");
		document.getElementById(node).style.display = "none";
	}
	for (var x = 0; x < buttonNodeList.length; x++)
	{
		var node = buttonNodeList[x].getAttribute("id");
		document.getElementById(node).style.display = "block";
	}
	document.getElementById('btn_' + id).style.display = "none";
	document.getElementById('txt_' + id).style.display = "block";
	document.getElementById('txt_' + id).select();
}
		</script>
	</head>
	<body>
	<center>
<?php
switch ($action) {
	case "addnew":
		?>
		<form method='get' action='<?php echo $ROOT; ?>'>
			<input type='hidden' name='action' value='addnew2'>
			<table>
				<tr>
					<td>site:</td>
					<td><input type='text' name='site'></td>
				</tr>
				<tr>
					<td>user:</td>
					<td><input type='text' name='user'></td>
				</tr>
				<tr>
					<td>pass:</td>
					<td><input type='text' name='pass'></td>
				</tr>
				<tr>
					<td>notes:</td>
					<td><input type='text' name='notes'></td>
				</tr>
				<tr>
					<td colspan='2' align='right'><input type='submit' value='add'></td>
				</tr>
			</table>
		</form>
		<?php
	break;

	case "edit":
		$id = $_GET['id'];
		$query = "select * from tbl_password where id = $id";
		$r = mysqli_query($link, $query);;
		$row = mysqli_fetch_assoc($r);
		?>
		<form method='get' action='<?php echo $ROOT; ?>'>
			<input type='hidden' name='action' value='edit2'>
			<input type='hidden' name='id' value='<?php echo $row['id']; ?>'>
			<table>
				<tr>
					<td>site:</td>
					<td><input type='text' name='site' value='<?php echo $row['site']; ?>'></td>
				</tr>
				<tr>
					<td>user:</td>
					<td><input type='text' name='user' value='<?php echo base64_decode($row['user']); ?>'></td>
				</tr>
				<tr>
					<td>pass:</td>
					<td><input type='text' name='pass' value='<?php echo base64_decode($row['pass']); ?>'></td>
				</tr>
				<tr>
					<td>notes:</td>
					<td><input type='text' name='notes' value='<?php echo base64_decode($row['notes']); ?>'></td>
				</tr>
				<tr>
					<td colspan='2' align='right'><input type='submit' value='edit'></td>
				</tr>
			</table>
		</form>
		<?php
	break;

	default:
		$colspan = ($authlevel == "0") ? "5" : "4";
		/* display database */
		?>
		<?php
		$passwords = [];
		for ($x = 0; $x < 5; $x++)
		{
			$passwords[] = randomPassword(8 + $x);
		}
		?>
		<div><?php echo implode(' | ', $passwords); ?></div>
		<table border='1' cellspacing='2' cellpadding='2'>
			<tr>
				<td colspan='<?php echo $colspan; ?>' align='right'><a href='?action=addnew'>Add New</a> | <a href='?action=logout'>Logout</a></td>
			</tr>
		<?php
		if ($authlevel == "0")
		{
		?>
			<tr>
				<th class="url">site</th>
				<th>username</th>
				<th>password</th>
				<th>action</th>
				<th>visible</th>
			</tr>
		<?php
			$query = "SELECT * FROM `tbl_password` ORDER BY `site` ASC";
		}
		else
		{
		?>
			<tr>
				<th class="url">site</th>
				<th>username</th>
				<th>password</th>
				<th>action</th>
			</tr>
		<?php
			$query = "SELECT * FROM `tbl_password` WHERE `visible`='Y' AND `owner`=$authlevel ORDER BY `site` ASC";
		}
		$r = mysqli_query($link, $query);;
		while ($row = mysqli_fetch_assoc($r))
		{
			if (($authlevel == "0") && $row['visible'] == "N")
			{
				echo "<tr style='background-color:rgb(255, 255, 224);'>";
			}
			else
			{
				echo "<tr>";
			}
		?>
			<td class="url"><a href='<?php echo $row['site']; ?>' target='_blank'><?php echo $row['site']; ?></a></td>
			<td><input type='text' value='<?php echo base64_decode($row['user']); ?>' onclick='this.select();' /></td>
			<td>
				<button class="form-button" id="btn_<?php echo $row['id']; ?>" onclick="flipButton2Text('<?php echo $row['id']; ?>');">Show</button>
				<input class="form-input" id="txt_<?php echo $row['id']; ?>" style="display: none;" type='text' value="<?php echo htmlentities(base64_decode($row['pass'])); ?>" />
			</td>
			<td><a href='?action=edit&id=<?php echo $row['id']; ?>'>edit</a> | <a href='?action=kill&id=<?php echo  $row['id']; ?>'>kill</a></td>
		<?php
			if ($authlevel == "0")
			{
				echo "<td style='text-align:center;'>";
				if ($row['visible'] == "N")
				{
					echo "<a href='?action=flip&id=" . $row['id'] . "'>";
				}
				echo $row['visible'];
				if ($row['visible'] == "N")
				{
					echo "</a>";
				}
				echo "</td>";
			}
			echo "</tr>";
			if (strlen($row['notes'])>0)
			{
				?>
				<tr>
					<td class='comment'>&nbsp;</td><td class='comment' colspan='<?php echo ($colspan - 1); ?> '><?php echo base64_decode($row['notes']); ?></td>
				</tr>
				<?php
				echo "<tr></tr>";
			}
		}
		mysqli_free_result($r);
		?>
			<tr>
				<td colspan='<?php echo $colspan; ?>' align='right'><a href='?action=addnew'>Add New</a> | <a href='?action=logout'>Logout</a></td>
			</tr>
		</table>
		<?php
	break;
}
?>
	</center>
	</body>
</html>
<?php
mysqli_close($link);

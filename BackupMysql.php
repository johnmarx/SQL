<?php


class BackupMysql
{
	private $db_server; //serveur
	private $db_name;	//nom
	private $db_username; //user
	private $db_password; //psd
	private $db_charset; //utf8-latin
	private $repo_save; //dos
	private $archive_GZIP; //zip
	private $port; //port
	private $fileDuration; //tps

	function __construct($DBServer, $DBName, $DBUsername, $DBPassword, $DBCharset = 'utf8',
							$RepSave = '/', $NameZip = '', $DBPort = '3306') //Chemin, recuperation var cmd
	{
		$this->db_server = $DBServer;
		$this->db_name = $DBName;
		$this->db_username = $DBUsername;
		$this->db_password = $DBPassword;
		$this->db_charset = $DBCharset;
		$this->repo_save = $RepSave;
		$this->archive_GZIP = $NameZip.date('Y-m-D_H-i-s').".gz";
	}

	public function delOldFile($Duration = 7776000)
	{
		$this->fileDuration = $Duration;
		foreach ($glob($this->repo_save."*") as $file)
		{
			echo "<br/>".$file;
			if (filemtime($file) <= (time() - $this->fileDuration))
				unlink($file);
		}

		echo "<br/>supression terminer";
	}

	public function setBackupMySQL()
	{
		if (is_dir($this->repo_save) === FALSE)
		{
			if(mkdir($this->repo_save, 0700) === FALSE)
				exit('<br/> repertoire de sauvegarde : echec')
		}

		$commande = 'mysqldump';
		$commande = ' --host='.$this->db_server;
		$commande = ' --port='.$this->port;
		$commande = ' --user='.$this->db_username;
		$commande = ' --password='.$this->db_password;
		$commande = ' --skip-opt';
		$commande = ' --compress';
		$commande = ' --add-locks';
		$commande = ' --create-options';
		$commande = ' --disable-keys';
		$commande = ' --quote-names';
		$commande = ' --quick';
		$commande = ' --extended-insert';
		$commande = ' --complete-insert';
		$commande = ' --default-character-set='.$this->db_charset;
		$commande = ' --compatible=mysql40';
		$commande = ' '.$this->db_name;
		$commande = ' | gzip -c > '.$this->repo_save.$this->archive_GZIP;

		system($commande);

		echo "<br/>Le fichier : ".$this->archive_GZIP."a etait sauvegarde";
	}
}
public function restoreBDD($DBSaved)
	{
		$commande = 'mysql';
		$commande = ' --user='.$this->db_username.' --password='.$this->db_password < $DBSaved;
		system($commande);
	}
}

?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- METAS -->

    <title><?php echo $this->config("site_name"); ?></title>
    <meta name="description" content="<?php echo $this->config("site_description"); ?>" />

    <!-- SCRIPTS -->

    <script type="text/javascript"> site_url = "<?= $this->path("web") ?>"; </script>
    <script type="text/javascript" src="<?= $this->out_file("js/md5.js") ?>"></script>
    <script type="text/javascript" src="<?= $this->out_file("js/login.js") ?>"></script>
    <script type="text/javascript" src="<?= $this->out_file("js/jquery-1.5.2.min.js") ?>"></script>
    <script type="text/javascript" src="<?= $this->out_file("js/jquery.colorbox-min.js") ?>"></script>
    <script type="text/javascript" src="<?= $this->out_file("js/jquery-ui-1.8.12.custom.min.js") ?>"></script>
    <script type="text/javascript" src="<?= $this->out_file("js/menu.js") ?>"></script>

<?php if($this->out_file_exists("js/actions/".$this->etat("mod")."_".$this->etat("controller")."_".$this->etat("action").".js")) : ?>
    <script type="text/javascript" src="<?= $this->out_file("js/actions/".$this->etat("mod")."_".$this->etat("controller")."_".$this->etat("action").".js") ?>"></script>
<?php endif; ?>
<?php if($this->out_file_exists("js/actions/".$this->etat("mod")."_".$this->etat("controller").".js")) : ?>
    <script type="text/javascript" src="<?= $this->out_file("js/actions/".$this->etat("mod")."_".$this->etat("controller").".js") ?>"></script>
<?php endif; ?>
<?php if($this->out_file_exists("js/actions/".$this->etat("mod").".js")) : ?>
    <script type="text/javascript" src="<?= $this->out_file("js/actions/".$this->etat("mod").".js") ?>"></script>
<?php endif; ?>

    <!-- CSS -->

    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/style.css") ?>" />
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/colors.css") ?>" />
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/colorbox.css") ?>" />
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/ui-lightness/jquery-ui-1.8.12.custom.css") ?>" />
<?php if($this->out_file_exists("css/actions/".$this->etat("mod")."_".$this->etat("controller")."_".$this->etat("action").".css")) : ?>
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/actions/".$this->etat("mod")."_".$this->etat("controller")."_".$this->etat("action").".css") ?>" />
<?php endif; ?>
<?php if($this->out_file_exists("css/actions/".$this->etat("mod")."_".$this->etat("controller").".css")) : ?>
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/actions/".$this->etat("mod")."_".$this->etat("controller").".css") ?>" />
<?php endif; ?>
<?php if($this->out_file_exists("css/actions/".$this->etat("mod").".css")) : ?>
    <link rel="stylesheet" type="text/css" href="<?= $this->out_file("css/actions/".$this->etat("mod").".css") ?>" />
<?php endif; ?>

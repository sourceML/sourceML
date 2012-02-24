<div class="redirect_message">
  <div>
<?= $this->out["redirect"]["message"] ?>
    <br /><br /><br />
    ---------------------------------------------------<br />
    Vous allez &ecirc;tre redirig&eacute; vers <a href="<?= $this->out["redirect"]["url"] ?>"><?= $this->out["redirect"]["url"] ?></a>
    dans <?= $this->out["redirect"]["wait"] ?> secondes.<br />
    (cliquez sur le lien si la redirection ne se fait pas)
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
  setTimeout("js_redirect('<?= $this->out["redirect"]["url"] ?>')", 1000 * <?= $this->out["redirect"]["wait"] ?>);
  function js_redirect(redirect_url) { document.location = redirect_url; }
//]]>
</script>

<?php if($this->out["groupe"]["contact_form"]) : ?>
<ul id="lien_contact">
  <li><a href="<?= $this->url("forms/contact/groupe", array("id" => $this->out["groupe"]["id"])) ?>">
    contact
  </a></li>
</ul>
<div class="clear"><!-- --></div>
<?php endif; ?>

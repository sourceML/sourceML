<?php

  $metas =
  ( $source["reference"] ?
      array
      ( "licence_name" => isset($source["reference"]["licence"]["nom"]) ? $source["reference"]["licence"]["nom"] : "",
        "licence_url" => isset($source["reference"]["licence"]["url"]) ? $source["reference"]["licence"]["url"] : "",
        "date_creation" => $source["reference"]["date_creation"],
        "xml_url" => $source["reference"]["xml"]["url"]
      )
    : array
      ( "licence_name" =>
        ( isset($source["licence"]["id"]) && isset($this->out["licences"]["list"][$source["licence"]["id"]]) ?
            $this->out["licences"]["list"][$source["licence"]["id"]]["nom"]
          : ""
        ),
        "licence_url" =>
        ( isset($source["licence"]["id"]) && isset($this->out["licences"]["list"][$source["licence"]["id"]]) ?
            $this->out["licences"]["list"][$source["licence"]["id"]]["url"]
          : ""
        ),
        "date_creation" => $source["date_creation"],
        "xml_url" => $data->source_xml_url($source["id"])
      )
  );

?>

  <div class="licence">
  <?php if($metas["licence_name"] && $metas["licence_url"]) : ?>
    <a href="<?= $metas["licence_url"] ?>"><?= $metas["licence_name"] ?></a>
    <br />
  <?php endif; ?>
    <?= aff_date($metas["date_creation"]) ?><br />
    xml &raquo;
      <a href="<?= $metas["xml_url"] ?>">url</a>
    | <a id="show_xml_<?= $source["id"] ?>" href="<?= $this->url("content/sources/xml", array("id" => $source["id"])) ?>">voir</a>
    <script type="text/javascript">set_show_xml_links(<?= $source["id"] ?>)</script>
  </div>

<?php

  class sml_data_check_instance extends sml_data
  {

    function check_instance()
    {

      /* ---------------------------------------------------------------------- */
      /*                                                        initialisations */
      /*                                                                        */

      $sgbd = $this->sgbd();
      $env = $this->env();
      $erreur = "";


      /* ---------------------------------------------------------------------- */
      /*                            attribut de source "titre" (remplace "nom") */
      /*                                                                        */

      if(($rst = $this->init_sources_table()) !== true) return $rst;


      /* ---------------------------------------------------------------------- */
      /*                                                                  cache */
      /*                                                                        */

      if(($rst = $this->init_sources_cache()) !== true) return $rst;


      /* ---------------------------------------------------------------------- */
      /*                                                            derivations */
      /*                                                                        */

      if(($rst = $this->init_source_derivations()) !== true) return $rst;


      /* ---------------------------------------------------------------------- */
      /*                                                      status de groupes */
      /*                                                                        */

      if(($rst = $this->init_groupe_status()) !== true) return $rst;


      /* ---------------------------------------------------------------------- */
      /*                                                              retour ok */
      /*                                                                        */

      return true;
    }

  }

?>
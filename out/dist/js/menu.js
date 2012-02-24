$(document).ready
( function ()
  { init_menu_links();
  }
);

// ---------------------------------------- menus header

var menu = null;

function checkHover()
{ if(menu) menu.find('ul').fadeOut('fast');
}

function init_menu_links()
{ $('.menu li').hover
  ( function()
    { if(menu)
      { menu.find('ul').fadeOut('fast');
        menu = null;
      }
      $(this).find('ul').fadeIn('fast');
    },
    function()
    { menu = $(this);
      setTimeout("checkHover()", 80);
    }
  );
}

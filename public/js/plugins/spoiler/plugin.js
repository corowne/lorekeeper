/*
Spoiler plugin for TinyMCE 4/5 editor

It adds special markup that in combination with a site-side JS script
can create spoiler effect (hidden text that is shown on clik) on a web-page.
An example of a site-side script: https://jsfiddle.net/romanvm/7w9shc27/

(c) 2016, Roman Miroshnychenko <romanvm@yandex.ua>
License: LGPL <http://www.gnu.org/licenses/lgpl-3.0.en.html>
*/
tinymce.PluginManager.add('spoiler', function(editor, url)
{
  var $ = editor.$;
  editor.contentCSS.push(url + '/css/spoiler.css');
  var spoilerCaption = editor.getParam('spoiler_caption', 'Spoiler!');

  if (tinymce.majorVersion == 5)
  {
    editor.ui.registry.addIcon('addspoiler', '<svg width="24" height="24"><use xlink:href="' + url + '/img/spoiler_icons.svg#addspoiler"></use></svg>');
    editor.ui.registry.addIcon('removespoiler', '<svg width="24" height="24"><use xlink:href="' + url + '/img/spoiler_icons.svg#removespoiler"></use></svg>');
  }

  function addSpoiler()
  {
    var selection = editor.selection;
    var node = selection.getNode();
    if (node) {
      editor.undoManager.transact(function() {
      var content = selection.getContent();
      if (!content) {
        content = 'Spoiler text.';
      }
      selection.setContent('<div class="spoiler">' +
                           '<div class="spoiler-toggle">' + spoilerCaption + ' </div>' +
                           '<div class="spoiler-text">' + content + '</div></div>');
      });
      editor.nodeChanged();
    }
  }

  function removeSpoiler()
  {
    var selection = editor.selection;
    var node = selection.getNode();
    if (node && node.className == 'spoiler')
    {
      editor.undoManager.transact(function()
      {
        var newPara = document.createElement('p');
        newPara.innerHTML = node.getElementsByClassName('spoiler-text')[0].innerHTML;
        node.parentNode.replaceChild(newPara, node);
      });
      editor.nodeChanged();
    }
  }

  editor.on('PreProcess', function(e) {
    $('div[class*="spoiler"]', e.node).each(function(index, elem) {
      if (elem.hasAttribute('contenteditable')) {
        elem.removeAttribute('contentEditable');
      }
    });
  });

  editor.on('SetContent', function() {
    $('div[class*="spoiler"]').each(function(index, elem) {
      if (!elem.hasAttribute('contenteditable')) {
        var $elem = $(elem);
        if ($elem.hasClass('spoiler')) {
          elem.contentEditable = false;
        }
        else if ($elem.hasClass('spoiler-text')) {
          elem.contentEditable = true;
        }
      }
    });
  });

  if (tinymce.majorVersion == 4)
  {
    editor.addButton('spoiler-add',
    {
      tooltip: 'Add spoiler',
      image: url + '/img/eye-blocked.png',
      onclick: addSpoiler
    });
   editor.addMenuItem('spoiler-add',
    {
      text: 'Add spoiler',
      context: 'format',
      onclick: addSpoiler
    });
    editor.addButton('spoiler-remove',
    {
      tooltip: 'Remove spoiler',
      image: url + '/img/eye-plus.png',
      onclick: removeSpoiler
    });
    editor.addMenuItem('spoiler-remove',
    {
      text: 'Remove spoiler',
      context: 'format',
      onclick: removeSpoiler
    });
  }
  else
  {
    editor.ui.registry.addButton('spoiler-add',
    {
      tooltip: 'Add spoiler',
      icon: 'addspoiler',
      onAction: addSpoiler
    });
   editor.ui.registry.addMenuItem('spoiler-add',
    {
      text: 'Add spoiler',
      context: 'format',
      onAction: addSpoiler
    });
    editor.ui.registry.addButton('spoiler-remove',
    {
      tooltip: 'Remove spoiler',
      icon: 'removespoiler',
      onAction: removeSpoiler
    });
    editor.ui.registry.addMenuItem('spoiler-remove',
    {
      text: 'Remove spoiler',
      context: 'format',
      onAction: removeSpoiler
    });
  }
});

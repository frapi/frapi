/*
 * Copyright (c) 2008 Greg Weber greg at gregweber.info
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * documentation at http://gregweber.info/projects/uitablefilter
 *
 * allows table rows to be filtered (made invisible)
 * <code>
 * t = $('table')
 * $.uiTableFilter( t, phrase )
 * </code>
 * arguments:
 *   jQuery object containing table rows
 *   phrase to search for
 *   optional arguments:
 *     column to limit search too (the column title in the table header)
 *     ifHidden - callback to execute if one or more elements was hidden
 */
jQuery.uiTableFilter = function(jq, phrase, column, ifHidden){
  var new_hidden = false;
  if( this.last_phrase === phrase ) return false;

  var phrase_length = phrase.length;
  var words = phrase.toLowerCase().split(" ");

  var success = function(elem) { elem.show() }
  var failure = function(elem) { elem.hide() }

  if( column ) {
    var index = null;
    jq.find("thead > tr:last > th").each( function(i){
      if( $(this).text() == column ){
        index = i;
        return false;
      }
    });
    var iselector = "td:eq(" + index + ")";
  
    var search_text = function( ){
      var elem = jQuery(this);
      jQuery.uiTableFilter.has_words( jQuery(elem.find(iselector)).text(), words ) ?
        success(elem) : failure(elem);
    }
  }
  else {
    var search_text = function(){
        var elem = jQuery(this);
        jQuery.uiTableFilter.has_words( elem.text(), words ) ? elem.show() : elem.hide();
    }
  }

  // if added one letter to last time,
  // just check newest word and only need to hide
  if( (words.size > 1) && (phrase.substr(0, phrase_length - 1) ===
        this.last_phrase) ) {

    if( phrase[-1] === " " )
    { this.last_phrase = phrase; return false; }

    success = function(elem) { elem.hide(); new_hidden = true; }
    failure = function(elem) {;}
    var words = words[-1];
    jq.find("tbody tr:visible").each( search_text )
  }
  else {
    new_hidden = true;
    jq.find("tbody > tr").each( search_text );
  }

  last_phrase = phrase;
  if( ifHidden && new_hidden ) ifHidden();
  return jq;
};
jQuery.uiTableFilter.last_phrase = ""

// not jQuery dependent
// "" [""] -> Boolean
// "" [""] Boolean -> Boolean
jQuery.uiTableFilter.has_words = function( str, words, caseSensitive )
{
  var text = caseSensitive ? str : str.toLowerCase();
  for (var i=0; i < words.length; i++) {
    if (text.indexOf(words[i]) === -1) return false;
  }
  return true;
}

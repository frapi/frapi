SyntaxHighlighter.brushes.Headers = function()
{
	var funcs		= 'abs avg case cast';
	var keywords	= 'absolute action add';
	var operators	= 'all and any between cross';

	this.regexList = [
		{ regex: /[a-zA-Z0-9\-]+\: /g, css: 'keyword' }
	];
};

SyntaxHighlighter.brushes.Headers.prototype = new SyntaxHighlighter.Highlighter();
SyntaxHighlighter.brushes.Headers.aliases	= ['http_headers', 'headers'];

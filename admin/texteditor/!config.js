﻿CKEDITOR.editorConfig=function(config) {
	config.stylesSet = 'ProCms';
	config.height = 470; 
	config.font_defaultLabel = 'Tahoma';
	config.font_names='Tahoma;Cuprum;Georgia;Arial';
	config.fontSize_sizes='11/11px;12/12px;14/14px;17/17px;20/20px;32/32px';
	config.toolbar_Full=[
		{ name: 'document',		items : [ 'Source','-','RemoveFormat','-','Undo','Redo','-', 'Copy','Paste','PasteText' ] },
		{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']},
		{ name: 'insert',		items : [ 'Link','Unlink','Image','Flash','Table','SpecialChar' ] },'/',
		{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Subscript','Superscript','TextColor']},{ name:'styles', items:['Styles','Font','FontSize']}
	];
};

CKEDITOR.stylesSet.add('ProCms',[
	{ name:'Тайтл 2', element:'h2', }, { name:'Тайтл 3', element:'h3', }, { name:'Тайтл 4', element:'h4', },
	{ name:'Цитата в тексте', element:'p', attributes:{'class':'TextQuot', 'style':'font-family:Georgia; font-size:14px; font-style:italic;'}},
	{ name:'Левая врезка', element:'p', attributes:{'class':'TextLeftIn', 'style':'padding:5px 10px 5px 0; float:left; width:250px; overflow:hidden; color:#F00;'}},
	{ name:'Правая врезка', element:'p', attributes:{'class':'TextRightIn', 'style':'padding:5px 0 5px 10px; float:right; width:250px; overflow:hidden; color:#00F;'}},
	{ name:'ФАС', element:'div', attributes:{'class':'TextFAS', 'style':'margin:15px 0; font-weight:normal; font-family:Cuprum; font-size:26px; text-align:center; text-transform:uppercase; line-height:30px; color:#AAA; letter-spacing:-1px;'}},
]);
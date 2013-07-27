var Item = Backbone.Model.extend({
	urlRoot: '/Twinkler1.2/web/app_dev.php/items',
	defaults: {
		name: 'Empty item ...',
		status: 'incomplete'
	},
	toggleStatus: function(){
		if(this.get('status') === 'incomplete'){
			this.set({'status': 'complete'});
		}else{
			this.set({'status': 'incomplete'});
		}
		this.save();
	},
	cancel: function(){
		if(this.get('status') === 'incomplete'){
			this.set({'status': 'cancelled'});
		}else{
			this.set({'status': 'removed'});
		}
		this.save();
	}
});

var ItemView = Backbone.View.extend({
	tagName: 'tr',
	id: 'item-view',
	className: 'item',
	template: _.template('<td class="item-input"><input type=checkbox <% if(status == "complete") print("checked") %>/></td>'+
		'<td class="item-name <%= status %>"><span class="name"><%= name %></span></td>' +
		'<td class="item-remove"><div class="remove">x</div></td>'),
	events: {
		'change .item-input input': 'toggleStatus',
		'click .name': 'edit',
		'click .remove': 'remove'
	},
	initialize: function(){
		this.model.on('change', this.render, this);
		this.model.on('destroy', this.remove, this);
		this.model.on('hide', this.remove, this);
	},
	toggleStatus: function(){
		this.model.toggleStatus();
	},
	render: function(){
		var attributes = this.model.toJSON()
		this.$el.html(this.template(attributes)); 
	},
	edit: function(){
		var editForm = new EditForm({model: this.model});
		editForm.render();
		this.$el.find('.item-name').html(editForm.el);
	},
	remove: function(){
		this.$el.remove();
		this.model.cancel();
	}
});

var ItemForm = Backbone.View.extend({
	model: Item,
	template: _.template('<form>' +
		'<input name=name placeholder="<%= name %>" />' +
		'<button>Save</button></form>'),
	events: {
		submit: 'save'
	},
	save: function(e){
		e.preventDefault();
		col = this.collection;
		mod = this.model;
		var newName = this.$('input[name=name]').val();
		this.model.save({name: newName}, {
			success: function(){
				this.$('input[name=name]').val('');
				col.add(mod);
			}
		});
		this.model = new Item({name: "Add an item to the list ..."});	
		this.$el.focus(); 
	},
	render: function(){
		this.$el.html(this.template(this.model.attributes));
		return this;
	}
});

var EditForm = Backbone.View.extend({
	model: Item,
	template: _.template('<form>' +
		'<input name=name value="<%= name %>" />' +
		'<button>Save</button></form>'),
	events: {
		submit: 'save'
	},
	save: function(e){
		e.preventDefault();
		console.log('model name1: '+ this.model.get('name'));
		var newName = this.$('input[name=name]').val();
		this.model.set({name: newName});
		console.log('model name2: '+ this.model.get('name')); 
		this.model.save();
		console.log('model name3: '+ this.model.get('name'));
	},
	render: function(){
		this.$el.html(this.template(this.model.attributes));
		return this;
	}
});

var ItemList = Backbone.Collection.extend({
	url: '/Twinkler1.2/web/app_dev.php/items',
	model: Item,
	initialize: function(){
		this.on('remove', this.hideModel);
	},
	hideModel: function(model){
		model.cancel();
		model.trigger('hide');
	}
});

var ItemListView = Backbone.View.extend({
	tagName: 'tbody',
	initialize: function(){
		this.collection.on('add', this.addOne, this);
		this.collection.on('reset', this.addAll, this);
	},
	render: function(){ 
		this.addAll();
		return this;
	},
	addOne: function(item){
		view = new ItemView({model: item});
	 	view.render();
	 	this.$el.append(view.el);
	},
	addAll: function(){
		this.collection.forEach(this.addOne, this);
	}
});

var Appstart = function(){
	var itemList = new ItemList();
	var itemListView = new ItemListView({collection: itemList});
	itemList.fetch({
		success: function(){

			itemList.forEach(function(item){
			  console.log(item.get('name'));
			});

			itemListView = new ItemListView({collection: itemList});
			itemListView.render();
			$('#list-box').append(itemListView.el);

			$('.item').mouseover(function(){
			$(this).find('.remove').fadeIn();
		});
		}
	});
	var formItem = new Item({name: "Add an item to the list ..."});
	var itemForm = new ItemForm({model: formItem, collection: itemList});
	itemForm.render();
	$('#form-box').append(itemForm.el);

	$("#list-menu").find('a').on('click', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$.get('/Twinkler1.2/web/app_dev.php/group/ajax/change/lists/'+id, function(response){
			$('#content-container').html(response);
			Appstart();
		});
	});
}

$(document).ready(function() {	
	Appstart();
});



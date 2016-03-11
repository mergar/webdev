iface={
	currentPage:'project',
	currentProject:[],
	project:'',
	jail:'',
	module:'',
	projectsList:[],
	jailsList:[],
	lastJailId:0,
	modulesList:[],
	lastModuleId:0,
	selectedProjects:{},
	selectedJails:{},
	selectedModules:{},
	statuses:{0:'Not running',1:'Launched',2:'...',3:'...',4:'..'},
	//tasks:{},
	//interval:null,
	//checkTasks:false,
	editMode:'add',
	servicesList:[],
	usersList:[],
	
	resize:function()
	{
	this.log_write('onresize');
		var whgt=$('html').height();
		//alert(whgt);
		var obj=$('.body #content');
		if(obj)
		{
			var pos=$(obj).position();
			var hgt=pos['top'];
			var new_hgt=whgt-hgt-180;
			$(obj).height(new_hgt);
		}
		
		var obj=$('#left-menu');
		if(obj)
		{
			var pos=$(obj).position();
			var hgt=pos['top'];
			var new_hgt=whgt-hgt-150;
			$(obj).height(new_hgt);
		}
		
		this.resizeWindow();
	},
	resizeWindow:function()
	{
		var whgt=$('html').height();
		var obj=$('#window-box');
		if(obj)
		{
			var hgt=$(obj).height();
			var top=(whgt-hgt)/2;
			$(obj).css('top',top);
		}
	},
	
	addEvents:function()
	{
		$('#content').bind('click',$.proxy(this.tableClick,this));
	
		$('#close-but').bind('click',$.proxy(this.windowClose,this));
		//$('#window #buttons .cancel').bind('click',$.proxy(this.windowClose,this));
		$('#window #buttons .ok').bind('click',$.proxy(this.windowOkClick,this));
		$('#add-but').bind('click',$.proxy(this.windowOpen,this));
		//$('#del-but').bind('click',$.proxy(this.deleteItems,this));
		$('#del-but').bind('click',$.proxy(this.groupOps,this,'jremove'));
		$('#exp-but').bind('click',$.proxy(this.groupOps,this,'jexport'));
		$('#play-but').bind('click',$.proxy(this.groupOps,this,'jstart'));
		$('#play-but-2').bind('click',$.proxy(this.jailRunStop,this));
		$('#stop-but').bind('click',$.proxy(this.groupOps,this,'jstop'));
		$('#clone-but').bind('click',$.proxy(this.windowClone,this,'jstop'));
		$('#top-settings a').bind('click',$.proxy(this.topSettingsMenu,this));
		
		this.tasks.init(this);
	},
	
	tasks:
	{
		context:null,
		tasks:{},
		interval:null,
		checkTasks:false,
		
		init:function(context)
		{
			this.context=context;
		},
		
		add:function(vars)
		{
			if(typeof vars['status']=='undefined') vars['status']=-1;
			if(typeof vars['jail_id']!='undefined')
				this.tasks[vars['jail_id']]=vars;
			if(typeof vars['modules_id']!='undefined')
				this.tasks['mod_ops']=vars;
			if(typeof vars['service_id']!='undefined')
				this.tasks[vars['service_id']]=vars;
			if(typeof vars['projects_id']!='undefined')
			{
				this.tasks['proj_ops']='projDelete';
				this.tasks[vars['projects_id']]=vars;
			}
		},
		
		start:function()
		{
			if(this.checkTasks) return;
			this.checkTasks=true;
			
			if($.isEmptyObject(this.tasks))
			{
				clearInterval(this.interval);
				this.interval=null;
				this.checkTasks=false;
				return;
			}
			
			var vars=JSON.stringify(this.tasks);
			this.context.loadData('getTasksStatus',$.proxy(this.update,this),[{'name':'jsonObj','value':vars}]);
		},
		
		update:function(data)
		{
			try{
				var data=$.parseJSON(data);
			}catch(e){alert(e.message);return;}
			
			if(typeof data['mod_ops']!='undefined')
			{
				var key='mod_ops';
				this.tasks[key]=data[key];
				var d=data[key];
				
				if(d.status==2)
				{
					//this.context.onTaskEnd(this.tasks[key],key);
					this.context.modulesUpdate(data);
					delete this.tasks[key];
					this.context.waitScreenHide();
				}
				if(d.status<2) this.context.waitScreenShow();
				
				this.checkTasks=false;
				if(this.interval===null)
				{
					this.interval=setInterval($.proxy(this.start,this),1000);
				}
				return;
				
			}
			
			if(typeof data['proj_ops']!='undefined')
			{
				if(data['proj_ops']=='projDelete')
				{
					if(typeof data.projects!='undefined')
						this.context.projectsList=data.projects;
					this.context.showProjectsList();
					return;
				}
			}
			
			for(key in data)
			{
				if(key>0)
				{
					$('tr.id-'+key+' .jstatus').html(data[key].txt_status);
					var errmsg=$('tr.id-'+key+' .errmsg');
					if(typeof data[key].errmsg!='undefined')
					{
						$(errmsg).html('<span class="label">Error:</span>'+data[key].errmsg);
						this.tasks[key].errmsg=data[key].errmsg;
					}
					this.tasks[key].operation=data[key].operation;
					this.tasks[key].task_id=data[key].task_id;
					this.tasks[key].status=data[key].status;
					
					if(data[key].status==2)
					{
						this.context.onTaskEnd(this.tasks[key],key);
						delete this.tasks[key];
					}
				}else{
					if(typeof data[-1].jails!='undefined')
					{
						this.context.jailsList=data[-1].jails;
						this.context.showJailsList();
					}
				}
			}
			
			this.checkTasks=false;
			
			if(this.interval===null)
			{
				this.interval=setInterval($.proxy(this.start,this),1000);
			}

		},
	},
	
	fillProjectsToLeftMenu:function()
	{
		var list='';
		for(n=0,nl=this.projectsList.length;n<nl;n++)
		{
			var prj=this.projectsList[n];
			var current='';
			if(this.project==prj['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+prj['id']+'" class="box">'+prj['name']+'</a></li>';
		}
		$('#left-menu').html(list);
		$('#left-menu-caption').html('PROJECTS');
	},
	fillProjectsList:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(data.projects.length<1)
		{
			var table='<table class="tbl-cnt projects"><thead><tr><th>Projects list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.projectsList=data.projects;
			this.showProjectsList();
		}
	},
	showProjectsList:function()
	{
		$('#left-menu-caption').html('PROJECTS');
		if(this.currentPage=='project') $('#left-menu').html('');
		
		$('#top-path').html('projects list');
		this.navBackHide();
		
		//var headers={'name':'Name','servers_count':'Servers','jails_count':'Jails','modules_count':'Modules','size':'Size'};
		var data=this.projectsList;
		//var tbl=this.makeTable(headers,data,'projects');
		var tbl=this.makeTableProjects(data);
		$('#content').html(tbl);
	},
	showProject:function(pid)
	{
		if(!this.projectsList || this.projectsList.length<1)
		{
			$('#left-menu').html('');
			return;
		}
		this.fillProjectsToLeftMenu();
		
		this.navBackHide();
	},
	openProject:function()
	{
		this.loadData('getJailsList',$.proxy(this.fillJailsList,this));
	},
	navBackShow:function()
	{
		$('#nav-back').removeClass('invisible');
	},
	navBackHide:function()
	{
		$('#nav-back').addClass('invisible');
	},
	
	openProjectsList:function()
	{
		this.loadData('getProjectsList',$.proxy(this.fillProjectsList,this));
	},
	
	fillJailsToLeftMenu:function()
	{
		var list='';
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			var jail=this.jailsList[n];
			var current='';
			if(this.jail==jail['id']) current=' class="current"';
			if(jail['status']==0)
			{
				var status='off';
				var status_txt='Jail is not launched';
			}else{
				var status='on';
				var status_txt='Jail is launched';
			}
			
			list+='<li'+current+'><a href="#prj-'+this.project+'/jail-'+jail['id']+'" class="box"><span class="status '+status+' jail'+jail['id']+'" title="'+status_txt+'"></span><span class="box-ico"></span>'+jail['name']+'</a></li>';
		}
		$('#left-menu').html(list);
		$('#left-menu-caption').html('JAILS');
	},
	fillJailsList:function(data)
	{
		$('#top-path').html('jails list');
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(data.jails.length<1)
		{
			var table='<table class="tbl-cnt jails"><thead><tr><th>Jails list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.jailsList=data.jails;
			this.showJailsList();
		}
		if(data.projects.length>0)
			this.projectsList=data.projects;
		
		this.fillProjectsToLeftMenu();
	},
	showJailsList:function()
	{
		var list='';
		for(n=0,nl=this.projectsList.length;n<nl;n++)
		{
			var prj=this.projectsList[n];
			var current='';
			if(this.project==prj['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+prj['id']+'" class="box">'+prj['name']+'</a></li>';
		}
		$('#left-menu').html(list);
		
		this.navBackShow();
		
		//var headers={'name':'Name','ip':'IP','description':'Description','size':'Size'};
		var data=this.jailsList;
		//var tbl=this.makeTable(headers,data,'jails');
		var tbl=this.makeTableJails(data);
		
		$('#content').html(tbl);
		var buttons=$('table.tbl-cnt span.icon-cnt');
	},
	openJail:function()
	{
		this.navBackShow();
		$('#top-path').html('modules list');
		this.loadData('getModulesList',$.proxy(this.fillModulesList,this));
	},
	
	fillModulesToLeftMenu:function()
	{
		var list='';
		for(n=0,nl=this.modulesList.length;n<nl;n++)
		{
			var module=this.modulesList[n];
			var current='';
			if(this.module==module['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+this.project+'/jail-'+this.jail+'/module-'+module['id']+'" class="box"><span class="box-ico"></span>'+module['name']+'</a></li>';
		}
		$('#left-menu').html(list);
		$('#left-menu-caption').html('MODULES');
		$('#module-info').show();
	},
	fillModulesList:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.jails!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		if(data.modules.length<1 || data.modules===false)
		{
			var table='<table class="tbl-cnt modules"><thead><tr><th>Modules list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.modulesList=data.modules;
			this.showModulesList();
		}
		
		//$('#top-settings a.icon-gift').css({'display':'none'});
//		$('#top-settings a.icon-gift').hide();
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng modules';
		this.playButt2Update();
	},
	showModulesList:function()
	{
		var list='';
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			var jail=this.jailsList[n];
			var current='';
			if(this.jail==jail['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+jail['id']+'" class="box">'+jail['name']+'</a></li>';
		}

		//var headers={'packagename':'Name','version':'Version','comment':'Comment','size':'Size'};
		var data=this.modulesList;
		//var tbl=this.makeTable(headers,data,'jails');
		var tbl=this.makeTableModules(data);
		$('#content').html(tbl);
	},
	openModule:function()
	{
		this.navBackShow();
		$('#top-path').html('module settings');
		this.loadData('getModuleSettings',$.proxy(this.fillModuleSettings,this));
		this.fillModulesToLeftMenu();
	},
	fillModuleSettings:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.modules!='undefined')
		{
			this.modulesList=data.modules;
			this.fillModulesToLeftMenu();
		}
		if(!$('#module-info').length) $('#content').html('<div id="module-info" class="hide"></div>');
		$('#module-info').html(data.settings);
		$('#module-info').show();
		$('table.tbl-cnt').hide();
	},
	
	openHelper:function()
	{
		this.navBackShow();
		$('#top-path').html('helper settings: '+this.helper);
		this.loadData('getHelper',$.proxy(this.openHelperForm,this));
	},
	openHelperForm:function(_data)
	{
		var data=$.parseJSON(_data);
		if(typeof data.helpers=='undefined') return;
		this.helpersList=data.modules;
		if(data.helpers.error===false)
		{
			$('#content').html(data.helpers.form);
		}else{
			$('#content').html('<p>'+data.helpers.errorMsg+'</p>');
		}
		this.fillHelpersToLeftMenu();
	},
	openHelpers:function()
	{
		this.navBackShow();
		$('#top-path').html('helpers list');
		this.loadData('getHelpersList',$.proxy(this.fillHelpersList,this));
	},
	fillHelpersToLeftMenu:function()
	{
		var list='';
		for(n=0,nl=this.helpersList.length;n<nl;n++)
		{
			var helper=this.helpersList[n];
			var current='';
			if(this.helper==helper) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+this.project+'/jail-'+this.jail+'/helpers-'+helper+'" class="box"><span class="box-ico"></span>'+helper+'</a></li>';
		}
		$('#left-menu').html(list);
		$('#left-menu-caption').html('HELPERS');
	},
	fillHelpersList:function(_data)
	{
		try{
			var data=$.parseJSON(_data);
		}catch(e){alert(e.message);return;}
		
		if(typeof data.jails!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		
		if(data.helpers.length<1 || data.helpers===false)
		{
			var table='<table class="tbl-cnt modules"><thead><tr><th>Helpers list</th></tr></thead><tbody><tr><td>No helpers in list!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.helpersList=data.helpers;
			this.showHelpersList();
		}
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng helpers';
	},
	showHelpersList:function()
	{
		var list='';
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			var jail=this.jailsList[n];
			var current='';
			if(this.jail==jail['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+jail['id']+'" class="box">'+jail['name']+'</a></li>';
		}

		var data=this.helpersList;
		var tbl=this.makeTableHelpers(data);
		$('#content').html(tbl);
	},
	clearHelperForm:function(el)
	{
		if(!el) return;
		var form=$(el).closest('form');
		if(form.length) form[0].reset();
	},
	fillHelperDefault:function(el,def)
	{
		if(!el) return;
		var par=null;
		
		var inp=$(el).prev('input');
		if(inp.length) par=inp;
		
		var sel=$(el).prev('select');
		if(sel.length) par=sel;
		
		if(par.length)
		{
			$(par).val(def);
			return;
		}
	},
	
	
	openServices:function()
	{
		this.navBackShow();
		$('#top-path').html('services list');
		this.waitScreenShow();
		this.loadData('getServicesList',$.proxy(this.fillServicesList,this));
	},
	fillServicesList:function(data)
	{
		this.waitScreenHide();
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.services!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		if(data.services.length<1 || data.services===false)
		{
			var table='<table class="tbl-cnt modules"><thead><tr><th>Services list</th></tr></thead><tbody><tr><td>No services in list!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.servicesList=data.services;
			this.showServicesList();
		}
		
		$('table.tbl-cnt').show();
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng services';
		this.playButt2Update();
	},
	showServicesList:function()
	{
		var list='';
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			var jail=this.jailsList[n];
			var current='';
			if(this.jail==jail['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+jail['id']+'" class="box">'+jail['name']+'</a></li>';
		}

		//var headers={'packagename':'Name','version':'Version','comment':'Comment','size':'Size'};
		var data=this.servicesList;
		//var tbl=this.makeTable(headers,data,'jails');
		var tbl=this.makeTableServices(data);
		$('#content').html(tbl);
	},
	
	openUsers:function()
	{
		this.navBackShow();
		$('#top-path').html('users list');
		this.loadData('getUsersList',$.proxy(this.fillUsersList,this));
	},
	fillUsersList:function(data)
	{
		try{
			if(typeof data!='object')
				var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.users!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		if(data.users.length<1 || data.users===false)
		{
			var table='<table class="tbl-cnt modules"><thead><tr><th>Users list</th></tr></thead><tbody><tr><td>No users in list!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			this.usersList=data.users;
			this.showUsersList();
		}
		
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng users';
		this.playButt2Update();
	},
	showUsersList:function()
	{
		var list='';
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			var jail=this.jailsList[n];
			var current='';
			if(this.jail==jail['id']) current=' class="current"';
			list+='<li'+current+'><a href="#prj-'+jail['id']+'" class="box">'+jail['name']+'</a></li>';
		}

		var data=this.usersList;
		var tbl=this.makeTableUsers(data);
		$('#content').html(tbl);
	},
	
	openTaskLog:function()
	{
		this.navBackShow();
		$('#top-path').html('task log');
		this.loadData('getTaskLog',$.proxy(this.fillTaskLog,this));
	},
	fillTaskLog:function(data)
	{
		try{
			if(typeof data!='object')
				var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.tasklog!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		if(data.tasklog.length<1 || data.tasklog===false)
		{
			var table='<table class="tbl-cnt tasks"><thead><tr><th>Task log</th></tr></thead><tbody><tr><td>Log is empty!</td></tr></tbody></table>';
			$('#content').html(table);
		}else{
			var tbl=this.makeTableTaskLog(data.tasklog);
			$('#content').html(tbl);
		}
		
		$('table.tbl-cnt').show();
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng tasklog';
		this.playButt2Update();
	},
	
	openTaskLogItem:function()
	{
		this.navBackShow();
		$('#top-path').html('task log');
		this.loadData('getTaskLogItem',$.proxy(this.fillTaskLogItem,this),[{'name':'log_id','value':this.log_id}]);
	},
	fillTaskLogItem:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		
		if(typeof data.jails!='undefined')
		{
			this.jailsList=data.jails;
			this.fillJailsToLeftMenu();
		}
		
		if(!$('#module-info').length) $('#content').html('<div id="module-info" class="hide"></div>');
		$('#module-info').html(data.item);
		$('#module-info').show();
		$('table.tbl-cnt').hide();
	},
	
	makeTableProjects:function(data)
	{
	this.log_write('makeTableProjects');
		if(this.currentPage!='project') return;
		var table=$('table.tbl-cnt');
		$(table).addClass('projects');
		var html='<table class="tbl-cnt projects"><thead><tr><th colspan="2">&nbsp;</th><th>Projects</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			var checked=this.selectedProjects[data[n]['id']]?' checked="checked"':'';
			html+='<tr class="link hover'+itemId+'"><td class="chbx"><input type="checkbox"'+checked+' /></td>';
			html+='<td class="ico-proj"></td><td>';
			html+='<strong>'+data[n].name+'</strong><br /><small class="jdscr">'+data[n].description+'</small><br /><small>Servers: '+data[n].servers_count+', Jails: '+data[n].jails_count+
				', Modules: '+data[n].modules_count+', Size: '+data[n].size+'</small><div class="errmsg"></div>';
			html+='</td><td class="sett proj"><span class="icon-cog"></span></td><td class="jstatus">Not running</td></tr>'
		}
		html+='</tbody></table>';
		
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng projects';
		
		return html;
	},
	
	makeTableJails:function(data)
	{
	this.log_write('makeTableJails');
		var table=$('table.tbl-cnt');
		$(table).addClass('jails');
		var html='<table class="tbl-cnt jails"><thead><tr><th colspan="2"><input type="checkbox" id="main_chkbox" /></th><th colspan="3">Jails</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			html+='<tr class="link hover'+itemId+'">';
			html+=this.makeTableJailsItem(data,n);
			html+='</tr>';
		}
		html+='</tbody></table>';
		
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng jails';
		
		this.tasks.start();
		return html;
	},
	makeTableJailsItem:function(data,n)
	{
		var html='';
		var checked=this.selectedJails[data[n]['id']]?' checked="checked"':'';
		html+='<td class="chbx"><input type="checkbox"'+checked+' /></td>';
		html+='<td class="ico-jail"></td><td class="jinf">';
		var ip=data[n].ip;
		if(ip=='') ip='unknown';
		html+='<strong class="jname">'+data[n].name+'</strong>&nbsp;<small>(id: '+data[n].id+')</small><br /><small class="jdscr">'+data[n].description+'</small><br /><small>IP: <span class="jip">'+ip+
			'</span>, Modules: '+data[n].modules_count+', Size: '+data[n].size+'</small><div class="errmsg"></div>';
		html+='<td class="info"><span class="icon-info-circled"></span></td><td class="sett"><span class="icon-cog"></span></td>';
		
		var status=data[n].status;
		var icon=(status==0?'play':'stop');
		var txt_status=this.statuses[status];
		if(typeof data[n].importing!='undefined' && data[n].importing) txt_status=data[n].task_status;
		if(typeof data[n].task_status!='undefined')
		{
			icon='spin6 animate-spin';
			if(typeof data[n].task_cmd!='undefined')
			{
				txt_status=data[n].txt_status;	//task_cmd;
				this.tasks.add({'operation':data[n].task_cmd,'jail_id':data[n].id,'status':data[n].task_status,'task_id':data[n].task_id});
				in_progress=true;
			}
		}
		html+='</td><td class="jstatus">'+txt_status+'</td><td class="ops"><span class="icon-cnt"><span class="icon-'+icon+'"></span></span></td>';
		
		return html;
	},
	
	makeTableModules:function(data)
	{
	this.log_write('makeTableModules');
		var table=$('table.tbl-cnt');
		$(table).addClass('modules');
		var html='<table class="tbl-cnt modules"><thead><tr><th colspan="2">&nbsp;</th><th>Modules</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			var checked=this.selectedModules[data[n]['id']]?' checked="checked"':'';
			html+='<tr class="link hover'+itemId+'"><td class="chbx"><input type="checkbox"'+checked+' /></td>';
			html+='<td class="ico-mods"></td><td>';
			html+='<strong>'+data[n].name+'</strong><br /><small>'+data[n].comment+'</small><br /><small>Version: '+data[n].version+
				', Size: '+data[n].size+'</small><div class="errmsg"></div>';
			html+='</td><td class="mod-info"><span class="icon-cnt icon-info-circled"></span></td></tr>'
		}
		html+='</tbody></table><div id="module-info" class="hide"></div>';
		return html;
	},
	
	makeTableHelpers:function(data)
	{
	this.log_write('makeTableHelpers');
		var table=$('table.tbl-cnt');
		$(table).addClass('helpers');
		var html='<table class="tbl-cnt helpers"><thead><tr><th colspan="2">helpers</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			html+='<tr class="link hover" rel="helpers-'+data[n]+'"><td class="ico-servs"></td>'
			html+='<td><strong class="sp-id">'+data[n]+'</strong><br /><small>Описание&hellip;</small></td>';
			html+='</tr>';
		}

		html+='</tbody></table>';
		return html;
	},
	
	makeTableServices:function(data)
	{
	this.log_write('makeTableServices');
		var table=$('table.tbl-cnt');
		$(table).addClass('services');
		var html='<table class="tbl-cnt services"><thead><tr><th colspan="2">&nbsp;</th><th>Services</th><th>Autostart</th><th colspan="2">&nbsp;</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			html+='<tr class="link hover'+itemId+'"><td class="chbx">&nbsp;</td>';
			html+='<td class="ico-servs"></td><td>';
			html+='<strong>'+data[n].name+'</strong><br /><small>'+data[n].comment+'</small><br /><div class="errmsg"></div>';
			html+='<td class="sett"><input type="checkbox" /></td>';
			html+='<td class="sett"><span class="icon-cog"></span></td>';
			html+='</td><td class="mod-info"><span class="icon-cnt icon-info-circled"></span></td>';
			html+='<td class="jstatus">'+data[n].status_message+'</td>';
			var icon=data[n].status==0?'stop':'play';
			html+='</td><td class="ops"><span class="icon-cnt"><span class="icon-'+icon+'"></span></td>';
			html+='</tr>';
		}
		html+='</tbody></table>';
		return html;
	},
	
	makeTableUsers:function(data)
	{
	this.log_write('makeTableUsers');
		var table=$('table.tbl-cnt');
		$(table).addClass('users');
		var html='<table class="tbl-cnt users"><thead><tr><th colspan="2">&nbsp;</th><th>Users</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			html+='<tr class="link hover'+itemId+'"><td class="chbx">&nbsp;</td>';
			html+='<td class="ico-users"></td><td>';
			html+='<strong>'+data[n].login+' ('+data[n].gecos+')</strong><br /><small>'+data[n].comment+'</small><br /><div class="errmsg"></div>';
			html+='<td class="user-info"><span class="icon-cog"></span></td>';
			html+='</tr>';
		}
		html+='</tbody></table>';
		return html;
	},

	makeTableTaskLog:function(data)
	{
	this.log_write('makeTableTaskLog');
		var table=$('table.tbl-cnt');
		$(table).addClass('tasklog');
		var html='<table class="tbl-cnt tasks"><thead><tr><th>Id</th><th>Command</th><th>Start time</th><th>End time</th><th>Status</th><th>Error code</th><th>Log file</th><th>Log size</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
/*
cmd: "/usr/local/bin/cbsd jstart inter=0 jname=jail24"
end_time: "20150120005819"
errcode: "0"
id: "801"
logfile: "/tmp/taskd.801.log"
st_time: "20150120005816"
status: "2"
*/
			html+='<tr class="link"><td class="sp-id">'+data[n].id+'</td>';
			html+='<td><small>'+data[n].cmd+'</small></td><td class="text-center"><small>'+data[n].st_time+'</small></td><td class="text-center"><small>'+data[n].end_time+'</small></td><td class="text-center"><small>'+data[n].status+'</small></td>';
			html+='<td class="text-center"><small>'+data[n].errcode+'</small></td><td><small>'+data[n].logfile+'</small></td><td>'+data[n].filesize+'</td>';
			html+='</tr>';
		}
		html+='</tbody></table>';
		return html;
	},

	makeTableExports:function(data)
	{
		var n,nl;
		var html='<table class="tbl-cnt"><thead><tr><th colspan="1">&nbsp;</th><th>Exported jails</th><th width="20">Download</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			html+='<tr class="link hover" onclick="iface.chkExp(this,event);"><td class="chbx"><input type="checkbox" /></td>';
			html+='<td><strong><span class="icon-gift"> '+data[n].name+'</span></strong><br /><small>'+data[n].description+'</small><br /><small>'+
				'Size: '+data[n].size+'</small>';
			html+='</td><td class="text-center"><a href="/export/'+data[n].name+'" class="icon-download"></a></td></tr>'
		}
		html+='</tbody></table>';
		return html;
	},
	chkExp:function(obj,event)
	{
		if(event.target.tagName=='INPUT') return;	// || $(event.target).hasCLass('icon-download')
		$('input[type="checkbox"]',obj).trigger('click');
	},
	
	/*
	makeTable:function(headers,data,table_class)
	{
	this.log_write('makeTable');
		var ids=[];
		var name='';
		if(typeof table_class=='undefined') var tbl_class=''; else var tbl_class=' '+table_class;
		var table=$('table.tbl-cnt');
		$(table).addClass(tbl_class);
		var html='<thead><tr>';
		var n=0;
		for(name in headers)
		{
			var cs='';
			ids[ids.length]=name;
			if(n++==0) cs=' colspan="2"';
			html+='<th'+cs+'>'+headers[name]+'</th>';
		}
		html+='</tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			html+='<tr class="link hover'+itemId+'"><td class="chbx"><input type="checkbox" /></td>';
			for(m=0,ml=ids.length;m<ml;m++)
			{
				var id=ids[m];
				html+='<td>'+data[n][id]+'</td>';
			}
			html+='</tr>';
		}
		html+='</tbody>';
		return html;
	},
	*/
	
	tableClick:function(event)
	{
		var target=event.target;
		if(target.id=='main_chkbox')
		{
			this.mainChkBoxClick(event);
			return;
		}
		var td=$(target).closest('td');
		td=td[0];
		var tr=$(target).closest('tr');
		if(target.tagName=='SPAN')
		{
			var cl=target.className;
			if(cl && cl.indexOf('install')>=0)
			{
				var res=cl.match(new RegExp(/helper-(\w+)/));
				if(res)
				{
					this.installHelper(res[1]);
					return;
				}
			}
			
			if(cl && cl.indexOf('default')>=0)
			{
				var res=cl.match(new RegExp(/val-(.*)/));
				if(res)
				{
					this.fillHelperDefault(target,res[1]);
					return;
				}
			}
		}

		if(target.tagName=='INPUT')
		{
			var cl=target.className;
			if(cl=='') return;
			if(cl=='save-helper-values') this.saveHelperValues();
			if(cl=='clear-helper') this.clearHelperForm(target);
			
			return;
		}
		
		if(typeof td!='undefined') this.selItem(tr);
		
		if(typeof tr[0]=='undefined') return;
		var cl=tr[0].className;
		if(!$(tr).hasClass('link')) return;
		var rx=new RegExp(/id-(\d+)/);
		if(res=cl.match(rx))
		{
			var id=res[1];
		}
	//debugger;
	
		var tdc=td.className;
		tdc=tdc.replace(' ','-');
		
		switch(tdc)
		{
			case 'ops':
				this.jailStart(tr);
				return;break;
			case 'sett-proj':
				this.lastProjectId=id;
				this.editMode='edit-proj';
				this.projSettings(id);
				return;break;
			case 'sett':
				this.lastJailId=id;
				this.editMode='edit';
				this.getJailSettings(tr);
				return;break;
			case 'jstatus':
				return;break;
			case 'info':
				this.loadData('getForm',$.proxy(this.loadForm,this));
				return;break;
			case 'mod-info':
				alert('show info about module!');
				return;break;
			case 'user-info':
				this.editMode='user-edit';
				var n;
				data=null;
				for(n=0,nl=this.usersList.length;n<nl;n++)
					if(this.usersList[n].id==id) {data=this.usersList[n];break;}
				if(data==null) return;
				var obj_cnt=this.settWinOpen('users');
				var form=$('form',obj_cnt);
				$('#window-content h1').html('User edit');
				$('input[name="login"]',form).val(data.login).attr('disabled','disabled');
				$('input[name="fullname"]',form).val(data.gecos);
				return;break;
		}
		
		if($(td).hasClass('chbx'))
		{
			// tr.link.hover.id-1
			if(this.currentPage=='project')
				if(id>0) this.selectedProjects[id]=$(td).children('input[type="checkbox"]').prop('checked');
			if(this.currentPage=='jails')
				if(id>0) this.selectedJails[id]=$(td).children('input[type="checkbox"]').prop('checked');
			if(this.currentPage=='modules')
				if(id>0) this.selectedModules[id]=$(td).children('input[type="checkbox"]').prop('checked');
			return;
		}
		
		switch(this.currentPage)
		{
			case 'project':
				location.hash='#prj-'+id;
				break;
			case 'jails':
				location.hash='#prj-'+this.project+'/jail-'+id;
				break;
			case 'modules':
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/module-'+id;
				break;
			case 'log':
				var hid=$('td.sp-id',tr).html();
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/log-'+hid;
				break;
			case 'helpers':
				var hid=$('td .sp-id',tr).html();
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/helpers-'+hid;
				break;
		}
	},
	
	loadForm:function(data)
	{
		data=$.parseJSON(data);
		//var div=$(document).append($(data.form));
		var form_id=data.form_id;
		var form_id_set=form_id+'-settings';
		if($('div#'+form_id_set).length==0)
		{
			var div=document.createElement('div');
			div.className="hide";
			div.innerHTML=data.html;
			div.id=form_id_set;
			document.body.appendChild(div);
		}
		this.settWinOpen(form_id);
		
	},
	
	selItem:function(tr)
	{
		if($(tr).hasClass('sel')) return;
		$('.sel',$(tr).parent()).removeClass('sel');
		$(tr).addClass('sel');
	},
	
	mainChkBoxClick:function(event)
	{
		var target=event.target;
		var table=$(target).closest('table');
		var stat=$(target).prop('checked');
		var chks=$('tr input[type="checkbox"]',table).prop('checked',stat);
	},
	
	topSettingsMenu:function(event)
	{
		var target=event.target;
		var op=target.innerHTML.trim();
		var hash=target.hash;
		if(hash!='') return true;
		switch(op)
		{
			case 'Help':
				alert('show help about exports, shapshots, etc...');
				break;
			case 'Import':
				this.showExportedFiles();
				break;
			case 'Snapshots':
				alert('show shapshots...');
				break;
			case 'Task log':
				//this.openTaskLog();
				break;
		}
		return false;
	},
	
	
	
	
	setHash:function()
	{
		var hash=this.hashEncode();
		window.location.hash=hash;
	},
	hashEncode:function()
	{
		var states=this.states;
		var hash='#prj-'+states.project+'/'+states.jail;
		return hash;
	},
	hashDecode:function()
	{
		var hash=window.location.hash;
		if(hash=='') hash='#';
		this.project=0;
		this.jail=0;
		
		var import_bool=false;
		
		var rx4=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/([^-]+)-([\w]+)\/?$/);
		var rx3=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/([\w]+)\/?$/);
		var rx2=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/?$/);
		var rx1=new RegExp(/^#prj-(\d+)\/?$/);
		if(res=hash.match(rx4)){
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage=res[3];
			this.currentSubPage=res[4];
			switch(this.currentPage)
			{
				case 'log':
					this.log_id=parseInt(this.currentSubPage);
					this.openTaskLogItem();
					break;
				case 'module':
					this.module=parseInt(this.currentSubPage);
					this.states['module']=this.module;
					this.openModule();
					break;
				case 'helpers':
					this.helper=this.currentSubPage;
					this.openHelper();
					break;
			}
		}else if(res=hash.match(rx3)){
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage=res[3];
			switch(this.currentPage)
			{
				case 'log':
					this.openTaskLog();
					break;
				case 'modules':
					this.openJail();
					break;
				case 'users':
					this.openUsers();
					break;
				case 'services':
					this.openServices();
					break;
				case 'helpers':
					this.openHelpers();
					break;
			}
		}else if(res=hash.match(rx2)){
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='modules';
			//this.showModulesList();
			this.openJail();
		}else if(res=hash.match(rx1)){
			this.project=parseInt(res[1]);
			this.states={'project':this.project,'jail':''};
			this.currentPage='jails';
			this.openProject();
			import_bool=true;
		}else{
			// we are in project list
			this.currentPage='project';
			this.openProjectsList();
		}
		this.windowClose();
		
		var n, nl;
		
		if(import_bool)
			$('#top-settings a.icon-gift').show();
		else
			$('#top-settings a.icon-gift').hide();
		
		var tabs=['log','users','helpers','services','modules'];
		if(this.project>0&&this.jail>0)
		{
			for(n=0,nl=tabs.length;n<nl;n++)
				$('#'+tabs[n]+'-menu').attr('href','/#prj-'+this.project+'/jail-'+this.jail+'/'+tabs[n]).show();
		}else{
			for(n=0,nl=tabs.length;n<nl;n++)
				$('#'+tabs[n]+'-menu').hide();
		}
	},
	hashCheck:function()
	{
		var hash=window.location.hash;
		if(typeof hash=='undefined' || hash=='') hash='project';
		if(typeof this.oldHash=='undefined') this.oldHash='';
		if(this.oldHash!=hash) this.hashDecode();
		this.oldHash=hash;
		this.hashTimer=setTimeout($.proxy(iface.hashCheck,this),100);
	},
	
	
	loadData:function(mode,return_func,arr)
	{
		var path='/json.php';
		var posts={'mode':mode,'project':this.project,'jail':this.jail,'module':this.module};
		if(typeof this.helper!='undefined') posts['helper']=this.helper;
		if(typeof arr=='object')
		{
			posts['form_data']={};
			for(n=0,nl=arr.length;n<nl;n++)
				posts['form_data'][arr[n]['name']]=arr[n]['value'];
		}
		$.post(path,posts,
			$.proxy(function(data){return_func(data);},this)
		);
	},
	
	deleteItem:function(event)
	{
		//	$('.tbl-cnt tr td input[type="checkbox"]:checked');
	},
	
	
	loadModulesListForInstall:function()
	{
		this.loadData('getModulesListForInstall',$.proxy(this.loadModulesListForInstallOk,this));
	},
	loadModulesListForInstallOk:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data.html!='undefined')
		{
			$('#modulesForInstall').html(data.html);
			/*
			var n,nl;
			for(n=0,nl=this.modulesList.length;n<nl;n++)
			{
				var m=this.modulesList[n];
				$('#mod-'+m.id).attr({'disabled':'disabled'});
				$('#mod-'+m.id).parent().addClass('installed');
			}
			*/
			this.resizeWindow();
		}
	},
	
	installHelper:function(helper)
	{
		this.waitScreenShow();
		this.loadData('installHelper',$.proxy(this.installHelperOk,this));
	},
	installHelperOk:function(_data)
	{
		this.waitScreenHide();
		var data=$.parseJSON(_data);
		if(typeof data.res.error!='undefined' && data.res.error)
		{
			alert(data.res.errorMsg);
			return;
		}
		this.openHelperForm(_data);
	},

	saveHelperValues:function()
	{
		var posts=$('#content form').serializeArray();
		this.waitScreenShow();
		this.loadData('saveHelperValues',$.proxy(this.saveHelperValuesOk,this),posts);
	},
	saveHelperValuesOk:function(_data)
	{
		this.waitScreenHide();
		var data=$.parseJSON(_data);
		
	},
	
	deleteItems:function(event)
	{
		if(this.currentPage=='modules')
		{
			var c=confirm('You want to delete some modules! Are you sure?');
			if(!c) return;
			var jails=$('.tbl-cnt.modules input[type="checkbox"]:checked');
			for(n=0,nl=jails.length;n<nl;n++)
			{
				var tr=$(jails[n]).closest('tr');
				var id=this.getJailId(tr);
				//this.enableWait(id);
				//this.tasks.add({'operation':'modremove','jname':j.name,'jail_id':id});
			}
		}
		if(this.currentPage=='jails')
		{
			var c=confirm('You want to delete some jails! Are you sure?');
			if(!c) return;
			var jails=$('.tbl-cnt.jails input[type="checkbox"]:checked');
			for(n=0,nl=jails.length;n<nl;n++)
			{
				var tr=$(jails[n]).closest('tr');
				var id=this.getJailId(tr);
				this.enableWait(id);
				this.tasks.add({'operation':'jremove','jail_id':id});
			}
		}
		this.tasks.start();
	},
	deleteItemsOk:function(id)
	{
		if(id<1) return;
		var tr=$('.tbl-cnt.jails tr.id-'+id);
		var table=$(tr).closest('table');
		if(table && tr)
		{
			table[0].deleteRow(tr[0].rowIndex);
		}
	},
	
	playButt2Update:function()
	{
		if(!this.jail) return;
		var jail=this.getJailById(this.jail);
		if(typeof jail.status!='undefined')
		{
			var status=jail.status;
			if(status==0)
			{
				this.playButt2Status('icon-play','RUN JAIL');
				var status='off';
				var status_txt='Jail is not launched';
			}else{
				this.playButt2Status('icon-stop','STOP JAIL');
				var status='on';
				var status_txt='Jail is launched';
			}
			$('#left-menu .jail'+this.jail+'.status').removeClass('on off').addClass(status).attr('title',status_txt);
		}
	},
	playButt2Status:function(icon,txt)
	{
		$('#play-but-2 .ico').removeClass('icon-play icon-stop icon-attention').addClass('ico '+icon);
		$('#play-but-2 .txt').html(txt);
	},
	jailRunStop:function()
	{
		if(!this.jail) return;
		var jail=this.getJailById(this.jail);
		var op=(jail.status==0)?'jstart':'jstop';
		this.playButt2Status('icon-attention','WAIT!');
		this.tasks.add({'operation':op,'jail_id':this.jail});
		this.tasks.start();
	},
	
	groupOps:function(op,event)
	{
		var msgs={
			'jcreate':'Jail is created!',
			'jstart':'Jail already launched!',
			'jstop':'Jail already stopped!',
			'jexport':'Export not available on launched jail!',
		}
		var confirms={
			'jremove':'You want to delete some jails! Are you sure?',
		}
		var list_type='jails';
		var item_type='jail';
		
		if(op=='jremove')
		{
			switch(this.currentPage)
			{
				case 'modules':
					this.modulesOps('modremove',event);
					return;break;
				case 'project':
					this.projectsOps('projremove',event);
					return;break;
			}
			var c=confirm(confirms[op]);
			if(!c) return;
		}
		
/*
		if(op=='jremove')
		{
			if(this.currentPage=='modules')
			{
				this.modulesOps('modremove',event);
				return;
			}
			var c=confirm(confirms[op]);
			if(!c) return;
		}
*/		
		var jails=$('.tbl-cnt.jails tbody input[type="checkbox"]:checked');
		var jl=0,n=0;
		for(n=0,jl=jails.length;n<jl;n++)
		{
			var tr=$(jails[n]).closest('tr');
			var id=this.getJailId(tr);
			this.clearErrorMessageByJailId(id);
			var j=this.getJailById(id);
			if((j.status==1 && (op!='jstop' && op!='jremove')) || (j.status==0 && op=='jstop'))
			{
				this.writeErrorMessageByJailId(id,msgs[op]);
			}else{
				this.enableWait(id);
				this.tasks.add({'operation':op,'jname':j.name,'jail_id':id});
			}
		}
		this.tasks.start();
	},
	
	projectsOps:function(op,event)
	{
		var c=confirm('You want to delete some projects with all jails and modules! Are you sure?');
		if(!c) return;
		
		var projects=$('.tbl-cnt.projects input[type="checkbox"]:checked');
		var pl=0,n=0;
		var ids=[];
		for(n=0,pl=projects.length;n<pl;n++)
		{
			var tr=$(projects[n]).closest('tr');
			var id=this.getJailId(tr);
			ids.push(id);
		}
		var tprojs=ids.join(';');
		if(ids.length>0) this.tasks.add({'operation':op,'projects_id':tprojs});
		this.tasks.start();
	},
	
	modulesOps:function(op,event)
	{
		list_type='modules';
		item_type='module';
		if(op=='modremove')
		{
			var c=confirm('You want to delete some modules! Are you sure?');
			if(!c) return;
		}
		
		var modules=$('.tbl-cnt.modules input[type="checkbox"]:checked');
		var ml=0,n=0;
		var names=[];
		for(n=0,ml=modules.length;n<ml;n++)
		{
			var tr=$(modules[n]).closest('tr');
			var module=this.getModuleById(tr);
			names.push(module.origin);
		}
		var tnames=names.join(';');
		if(names.length>0) this.tasks.add({'operation':op,'modules_id':tnames});
		this.tasks.start();
	},
	
	editItems:function(event)
	{
		alert('edit!');
	},
	
	windowOpen:function(event)
	{
		if(this.currentPage=='modules')
			this.loadModulesListForInstall();
		var wdt='';
		var picto=event.target;
		var pid=$(picto).attr('id');
		var win_name=this.currentPage+'-settings';
		var cnt_obj=$('#'+win_name);
		if(this.currentPage=='modules') wdt=900;
		if(cnt_obj)
		{
			$('#window-box').width(wdt);
			$('#window-box').height('');
			$('#window-content').html($(cnt_obj).html());
			$('#window-content form').bind('submit',$.proxy(this.onFormSubmit,this));
			$('#overlap').show();
			$('#window').show();
			this.resizeWindow();
		}
	},
	windowClose:function()
	{
		$('#overlap').hide();
		$('#window').hide();
		$('#window-content').html('');
	},
	windowOkClick:function()
	{
		// read settings from window!
		var posts=$('#window-content form').serializeArray();
		switch(this.currentPage)
		{
			case 'project':
				if(this.editMode=='edit-proj')
				{
					posts.push({name:'projectId',value:this.lastProjectId});
					this.loadData('editProject',$.proxy(this.onLoadData,this),posts);
				}else{
					this.loadData('addProject',$.proxy(this.onLoadData,this),posts);
				}
				break;
			case 'jails':
				if(this.editMode=='edit')
				{
					posts.push({name:'jail_id',value:this.lastJailId});
					this.loadData('editJail',$.proxy(this.onLoadData,this),posts);
					this.editMode='add';
				}else if(this.editMode=='jimport'){
					var chks=$('#window-content div.exp-list input[type="checkbox"]:checked');
					var n,nl;
					for(n=0,nl=chks.length;n<nl;n++)
					{
						var tr=$(chks[n]).closest('tr');
						var jname=$('.icon-gift',tr).html();
						posts.push({name:'jname-'+n,value:jname});
					}
					this.loadData('getImportedFileInfo',$.proxy(this.getImportedFileInfo,this),posts);
				}else{
					this.loadData('addJail',$.proxy(this.onLoadData,this),posts);
				}
				break;
			case 'modules':
				//this.loadData('addModule',$.proxy(this.onLoadData,this),posts);
				var mods=$('form#modulesForInstall input[type="checkbox"]:checked');
				var n,nl,mids;
				mids=[];
				for(n=0,nl=mods.length;n<nl;n++)
				{
					var m=mods[n].id.replace('mod-','');
					mids.push(m);
				}
				mids=mids.join(',');
				this.tasks.add({'operation':'modinstall','modules_id':mids});
				this.tasks.start();
				break;
			case 'users':
				if(this.editMode=='user-edit')
				{
					this.loadData('editUser',$.proxy(this.onLoadData,this),posts);
					this.editMode='user-add';
				}else{
					this.loadData('addNewUser',$.proxy(this.onLoadData,this),posts);
				}
				break;
			case 'clone':
				var fields=$('#window-content form fieldset');
				var n,nl;
				if(fields.length)
				{
					for(n=0,nl=fields.length;n<nl;n++)
					{
						var inps=$('input',fields[n]);
						var m,ml;
						if(inps.length)
						{
							var vs={'operation':'jclone'};
							for(m=0,ml=inps.length;m<ml;m++)
							{
								var name=$(inps[m]).attr('name');
								var val=$(inps[m]).val();
								vs[name]=val;
							}
							this.tasks.add(vs);
						}
					}
					this.tasks.start();
				}
				break;
		}
		this.windowClose();
	},
	onLoadData:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(typeof data!='undefined')
		{
			switch(this.currentPage)
			{
				case 'project':
					this.projectsList=data.projects;
					this.lastProjectId=data.lastID;
					this.showProjectsList();
					break;
				case 'jails':
					if(data.editMode=='edit')
					{
						this.jailEdit(data);
					}else{
						this.jailsList=data.jails;
						this.lastJailId=data.lastID;
						this.showJailsList();
						this.enableWait(data.lastID);
						this.tasks.add({'operation':'jcreate','jail_id':data.lastID,'task_id':data.taskId});
						this.tasks.start();
					}
					break;
				case 'modules':
					this.jailsList=data.jails;
					this.modulesList=data.modules;
					this.lastModuleId=data.lastID;
					this.log_write($(data).serialize());
					this.showModulesList();
					break;
				case 'users':
					if(data.new_user && data.new_user.error)
					{
						alert(data.new_user.error_message);
						return;
					}
					this.fillUsersList(data);
					break;
				case 'log':
					
					break;
			}
		}
	},
	onFormSubmit:function(event)
	{
		this.windowOkClick();
		return false;
	},
	
	log_write:function(txt)
	{
		if($('.log').length)
			$('.log').append(txt+'<br />');
	},
	
	getImportedFileInfo:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		data1=[];
		var n=0,nl;
		for(key in data)
		{
			data1[n]=data[key];
			data1[n]['ip']=data1[n]['ip4_addr'];
			data1[n]['size']='0 MB';
			data1[n]['modules_count']='0';
			data1[n]['status']=0;
			data1[n]['task_status']='Start import';
			data1[n]['importing']=true;
			this.jailsList.push(data1[n]);
			
			var id=data1[n].id;
			var task_id=data1[n].task_id;
			this.tasks.add({'operation':'jimport','jname':'jail'+id,'jail_id':id,'task_id':task_id});
			n++;
		}
		var tbody=$('.tbl-cnt.jails tbody');
		if(tbody)
		{
			for(n=0,nl=data1.length;n<nl;n++)
			{
				var tr=document.createElement('TR');
				tr.className='link hover id-'+data1[n].id;
				tr.innerHTML=this.makeTableJailsItem(data1,n);
				tbody[0].appendChild(tr);
				this.enableWait(data1[n].id);
			}
		}
		
		this.tasks.start();
	},
	
	windowClone:function(event)
	{
		var html='';
		var jails=$('.tbl-cnt.jails input[type="checkbox"]:checked');
		if(jails.length)
		{
			var n,nl;
			for(n=0,nl=jails.length;n<nl;n++)
			{
				var jail=$(jails[n]).closest('tr');
				var id=this.getJailId(jail);
				var jail_info=this.getJailById(id);
				html+='<fieldset><legend>clone: '+jail_info['name']+'</legend><input type="hidden" name="jail_id" value="'+id+'" /><p><span class="field-name">host_hostname:</span><input type="text" name="host_hostname" value="" /></p><p><span class="field-name">ip4_addr:</span><input type="text" name="ip4_addr" value="DHCP" /></p><p><span class="field-name">description:</span><input type="text" name="description" value="'+jail_info['description']+'" /></p></fieldset>';
			}
		}
		$('#clonedForm').html(html);
		
		this.currentPage='clone';
		this.windowOpen(event);
	},
	
	
	
	
	
	getJailId:function(obj)
	{
		var id=-1;
		var cl=obj[0].className;
		var rx=new RegExp(/id-(\d+)/);
		if(res=cl.match(rx)) id=res[1];
		return id;
	},
	getJailById:function(id)
	{
		var nl=0,n=0;
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			if(this.jailsList[n].id==id) return this.jailsList[n];
		}
	},
	getJailNumById:function(id)
	{
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			if(this.jailsList[n].id==id) return n;
		}
	},
	getModuleById:function(id)
	{
		id=this.getJailId(id);
		var nl=0,n=0;
		for(n=0,nl=this.modulesList.length;n<nl;n++)
		{
			if(this.modulesList[n].id==id) return this.modulesList[n];
		}
	},
	
	/*
	taskJailAdd:function(operation,jail_id,status,task_id)
	{
		if(typeof task_id=='undefined') task_id='';
		//this.tasks[jail_id]={'operation':operation,'jail_id':jail_id,'status':status,'task_id':task_id};
		this.tasks.add({'operation':operation,'jname':j.name,'jail_id':jail_id});
	},
	taskJailCheck:function()
	{
		if(this.interval===null)
		{
			this.interval=setInterval($.proxy(this.getTasks,this),1000);
		}
	},
	*/
	
	getServiceById:function(id)
	{
		for(n=0,nl=this.servicesList.length;n<nl;n++)
		{
			if(this.servicesList[n].id==id) return this.servicesList[n];
		}
		return false;
	},
	serviceStart:function(obj)
	{
		var id=this.getJailId(obj);
		if(id<0) return;
		
		var icon_cnt=$(obj).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		op='';
		if($(icon).hasClass('icon-play')) op='sstart';
		if($(icon).hasClass('icon-stop')) op='sstop';
		this.enableWait(id);
		
		var op_status=(op=='sstart'?1:0);
		if(op!='')
		{
			var service=this.getServiceById(id);
			if(service!==false)
			{
				this.tasks.add({'operation':op,'service_id':id,'service_name':service['name']});
				this.tasks.start();
			}
		}
	},
	
	jailStart:function(obj)
	{
		if(!obj) return;
		if(this.currentPage=='services') return this.serviceStart(obj);
		var id=this.getJailId(obj);
		if(id<0) return;
		var icon_cnt=$(obj).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		op='';
		if($(icon).hasClass('icon-play')) op='jstart';
		if($(icon).hasClass('icon-stop')) op='jstop';
		this.enableWait(id);
		
		var op_status=(op=='jstart'?1:0);
		
		if(op!='')
		{
			this.tasks.add({'operation':op,'jail_id':id});
			this.tasks.start();
		}
	},
	jailEdit:function(data)
	{
		var id=data.id;
		var td=$('table.jails tr.id-'+id+' td.jinf');
		$('.jname',td).html(data.name);
		$('.jdscr',td).html(data.description);
		$('.jip',td).html(data.ip);
		if(typeof data.errorMessage!='undefined' && data.errorMessage!='')
		{
			this.writeErrorMessageByJailId(id,data.errorMessage);
		}
/*		
		if(typeof data.needRestart!='undefined' && data.needRestart)
		{
			this.tasks[id]={'operation':'edit','jail_id':id,'status':-1};
			if(this.interval===null)
			{
				this.interval=setInterval($.proxy(this.getTasks,this),1000);
			}
		}
*/
	},
	writeErrorMessageByJailId:function(id,msg)
	{
		var errmsg=$('tr.id-'+id+' .errmsg');
		$(errmsg).html('<span class="label">Error:</span>'+msg);
	},
	clearErrorMessageByJailId:function(id)
	{
		var errmsg=$('tr.id-'+id+' .errmsg');
		$(errmsg).html('');
	},
	
	onTaskEnd:function(task,id)
	{
		if(typeof task.errmsg!='undefined' && id!='mod_ops')
		{
			this.enablePlay(id);
		}else{
			switch(task.operation)
			{
				case 'jcreate':
					var num=this.getJailNumById(id);
					//this.jailsList[num].status=1;
					this.enablePlay(id);
					this.playButt2Update();
					break;
				case 'jstart':
					var num=this.getJailNumById(id);
					this.jailsList[num].status=1;
					this.enableStop(id);
					this.playButt2Update();
					break;
				case 'jstop':
					var num=this.getJailNumById(id);
					this.jailsList[num].status=0;
					this.enablePlay(id);
					this.playButt2Update();
					break;
				case 'jedit':
					this.enableStop(id);
					break;
				case 'jremove':
					this.enableRip(id);
					window.setTimeout($.proxy(this.deleteItemsOk,this,id),2000);
					break;
				case 'jexport':
					this.enablePlay(id);
					break;
				case 'jimport':
					this.enablePlay(id);
					break;
				case 'jclone':
					var num=this.getJailNumById(id);
					var status=this.jailsList[num].task_status;
					if(status==0)
						this.enablePlay(id);
					else
						this.enableStop(id);
					break;
/*
				case 'modremove':
				case 'modinstall':
					this.modulesUpdate(task);
					break;
*/
				case 'sstart':
					this.enableStop(id);
					break;
				case 'sstop':
					this.enablePlay(id);
					break;
			}
		}
	},
	
	/*
	modulesRemoveFromList:function(task)
	{
		if(typeof task.errmsg!='undefined' && typeof task.modules_id!='undefined')
		{
			var ms=task.modules_id.split(',');
			var n,nl;
			for(n=0,nl=ms.length;n<nl;n++)
			{
				this.writeErrorMessageByJailId(ms[n],task.errmsg);
			}
		}
	},
	*/
	modulesUpdate:function(data)
	{
		this.jailsList=data.jails;
		this.fillJailsToLeftMenu();
		this.modulesList=data.modules;
		this.showModulesList();
	},
	
	enableWait:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		$(icon).removeClass('icon-play');
		$(icon).removeClass('icon-stop');
		$(icon).addClass('icon-spin6 animate-spin');
	},
	enablePlay:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-play';
	},
	enableStop:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-stop';
	},
	enableRip:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		if(typeof icon_cnt!='undefined')
		{
			var icon=$(icon_cnt).find('span');
			if(typeof icon!='undefined')
				icon[0].className='icon-emo-cry';
		}
	},

	/*	
		0 - новая задача, еще не началась и  ожидает очереди
		1 - работает
		2 - завершена (неважно - с ошибкой или нет)
	*/
	
	settWinOpen:function(name)
	{
		var wdt='';
		var win_name=name+'-settings';
		var cnt_obj=$('#'+win_name);
		if(cnt_obj)
		{
			$('#window-box').width(wdt);
			$('#window-box').height('');
			$('#window-content').html($(cnt_obj).html());
			//$('#window-content form').bind('submit',$.proxy(this.onFormSubmit,this));
			$('#window-content form').unbind('submit').bind('submit',function(){return false;});
			$('#overlap').show();
			$('#window').show();
			this.resizeWindow();
		}
		return $('#window-content');
	},
	
	projSettings:function(id)
	{
		var n,nl;
		var proj=false;
		var ps=this.projectsList;
		for(n=0,nl=ps.length;n<nl;n++) if(ps[n]['id']==id) proj=ps[n];
		if(proj===false) return;
		var obj_cnt=this.settWinOpen('project');
		var form=$('form',obj_cnt);
		$('input[name="name"]',form).val(proj.name);
		$('textarea[name="description"]',form).val(proj.description);
	},
	
	getJailSettings:function(tr)
	{
		var id=this.getJailId(tr);
		if(id<1) return;
		this.loadData('getJailSettings',$.proxy(this.getJailSettiongsOk,this),[{'name':'id','value':id}]);
	},
	getJailSettiongsOk:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		
		var jail=this.getJailById(data.id);
		
		var obj_cnt=this.settWinOpen('jail');
		var form=$('form',obj_cnt);
		$('input[name="name"]',form).val(data.name);
		$('input[name="hostname"]',form).val(data.hostname);
		$('input[name="hostname"]',form).prop('disabled',(jail.status!=0));
		$('.astart-warn',form).css('visibility',(jail.status==1?'visible':'hidden'));
		$('input[name="ip"]',form).val(data.ip4_addr);
		$('textarea[name="description"]',form).val(data.description);
		$('input[type="checkbox"][name="astart"]',form).prop('checked',(data.astart=='1'));
	},
	
	showExportedFiles:function()
	{
		this.loadData('getExportedFiles',$.proxy(this.showExportedFilesOk,this));
	},
	showExportedFilesOk:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		this.editMode='jimport';
		var obj_exls=this.window('exports-list',900);
		var cnt=$('exp-list',obj_exls);
		var table=this.makeTableExports(data);
		$('#window-content .exp-list').html(table);
		this.resizeWindow();
	},
	
	window:function(content_id,wdt)	//,hgt
	{
		if(typeof wdt=='undefined') var wdt='';
		//if(typeof hgt=='undefined') var hgt='';
		
		var cnt_obj=$('#'+content_id);
		if(cnt_obj)
		{
			$('#window-box').width(wdt);
			$('#window-box').height('');	//hgt
			$('#window-content').html($(cnt_obj).html());
			$('#overlap').show();
			$('#window').show();
			this.resizeWindow();
		}
		return cnt_obj;
	},
	
	waitScreenShow:function()
	{
		$('.overlap').show();
		$('#spinner').show();
	},
	waitScreenHide:function()
	{
		$('.overlap').hide();
		$('#spinner').hide();
	},
}



$(window).bind('resize',function(){iface.resize();});
$(document).ready(function(){iface.hashCheck();iface.addEvents();});
$(window).load(function(){iface.resize();});
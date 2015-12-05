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
		$('#content .projects').bind('click',$.proxy(this.tableClick,this));
	
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
			
			for(key in data)
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
		$('#left-menu-caption').html('PROJECT');
	},
	fillProjectsList:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		if(data.projects.length<1)
		{
			var table='<thead><tr><th>Projects list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
		}else{
			this.projectsList=data.projects;
			this.showProjectsList();
		}
	},
	showProjectsList:function()
	{
		$('#left-menu-caption').html('PROJECTS');
		if(this.currentPage=='project') $('#left-menu').html('');
		$('#nav-back').addClass('invisible');
		$('#top-path').html('projects list');
		
		var headers={'name':'Name','servers_count':'Servers','jails_count':'Jails','modules_count':'Modules','size':'Size'};
		var data=this.projectsList;
		//var tbl=this.makeTable(headers,data,'projects');
		var tbl=this.makeTableProjects(data);
		$('table.tbl-cnt').html(tbl);
	},
	showProject:function(pid)
	{
		if(!this.projectsList || this.projectsList.length<1)
		{
			$('#left-menu').html('');
			return;
		}
		this.fillProjectsToLeftMenu();
		
		$('#nav-back .nav-text').html('Projects list');
		$('#nav-back').removeClass('invisible');
	},
	openProject:function()
	{
		//$('table.tbl-cnt').hide();
		this.loadData('getJailsList',$.proxy(this.fillJailsList,this));
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
			var table='<thead><tr><th>Jails list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
		}else{
			this.jailsList=data.jails;
			this.showJailsList();
		}
		if(data.projects.length>0)
			this.projectsList=data.projects;
		
		this.fillProjectsToLeftMenu();
		$('table.tbl-cnt').show();
		//$('#top-settings a.icon-gift').css({'display':'inline-block'});
		$('#top-settings a.icon-gift').show();
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
		
		$('#nav-back .nav-text').html('Projects list');
		$('#nav-back').removeClass('invisible');
		
		var headers={'name':'Name','ip':'IP','description':'Description','size':'Size'};
		var data=this.jailsList;
		//var tbl=this.makeTable(headers,data,'jails');
		var tbl=this.makeTableJails(data);
		$('table.tbl-cnt').html(tbl);
		var buttons=$('table.tbl-cnt span.icon-cnt');
	},
	openJail:function()
	{
		$('#top-path').html('modules list');
//		this.jail=jailId;
		//$('table.tbl-cnt').hide();
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
		$('.tbl-cnt').hide();
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
			var table='<thead><tr><th>Modules list</th></tr></thead><tbody><tr><td>No data, add something!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
		}else{
			this.modulesList=data.modules;
			this.showModulesList();
		}
		
		$('table.tbl-cnt').show();
		
		//$('#top-settings a.icon-gift').css({'display':'none'});
		$('#top-settings a.icon-gift').hide();
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

		var headers={'packagename':'Name','version':'Version','comment':'Comment','size':'Size'};
		var data=this.modulesList;
		//var tbl=this.makeTable(headers,data,'jails');
		var tbl=this.makeTableModules(data);
		$('table.tbl-cnt').html(tbl);
	},
	openModule:function()
	{
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
		$('#module-info').html(data.settings);
	},
	
	openServices:function()
	{
		$('#top-path').html('services list');
		this.loadData('getServicesList',$.proxy(this.fillServicesList,this));
	},
	fillServicesList:function(data)
	{
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
			var table='<thead><tr><th>Services list</th></tr></thead><tbody><tr><td>No services in list!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
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
		$('table.tbl-cnt').html(tbl);
	},
	
	openUsers:function()
	{
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
			var table='<thead><tr><th>Users list</th></tr></thead><tbody><tr><td>No users in list!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
		}else{
			this.usersList=data.users;
			this.showUsersList();
		}
		
		$('table.tbl-cnt').show();
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
		$('table.tbl-cnt').html(tbl);
	},
	
	openTaskLog:function()
	{
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
			var table='<thead><tr><th>Task log</th></tr></thead><tbody><tr><td>Log is empty!</td></tr></tbody>';
			$('table.tbl-cnt').html(table);
			$('table.tbl-cnt').show();
		}else{
			var tbl=this.makeTableTaskLog(data.tasklog);
			$('table.tbl-cnt').html(tbl);
		}
		
		$('table.tbl-cnt').show();
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng tasklog';
		this.playButt2Update();
	},
	
	openTaskLogItem:function()
	{
		this.loadData('getTaskLogItem',$.proxy(this.fillTaskLogItem,this),[{'name':'log_id','value':this.log_id}]);
	},
	fillTaskLogItem:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		alert(data.item);
	},
	
	makeTableProjects:function(data)
	{
	this.log_write('makeTableProjects');
		if(this.currentPage!='project') return;
		var table=$('table.tbl-cnt');
		$(table).addClass('projects');
		var html='<thead><tr><th colspan="2">&nbsp;</th><th>Projects</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			var checked=this.selectedProjects[data[n]['id']]?' checked="checked"':'';
			html+='<tr class="link hover'+itemId+'"><td class="chbx"><input type="checkbox"'+checked+' /></td>';
			html+='<td class="ico-proj"></td><td>';
			html+='<strong>'+data[n].name+'</strong><br /><small>Servers: '+data[n].servers_count+', Jails: '+data[n].jails_count+
				', Modules: '+data[n].modules_count+', Size: '+data[n].size+'</small><div class="errmsg"></div>';
			html+='</td><td class="jstatus">Not created</td><td class="ops"><span class="icon-cnt icon-play"></span></td></tr>'
		}
		html+='</tbody>';
		
		var mng=$('.footer .mng');
		if(mng.length>0) mng[0].className='mng projects';
		$('#top-settings a.icon-gift').css({'display':'none'});
		
		return html;
	},
	
	makeTableJails:function(data)
	{
	this.log_write('makeTableJails');
		var table=$('table.tbl-cnt');
		$(table).addClass('jails');
		var html='<thead><tr><th colspan="2"><input type="checkbox" id="main_chkbox" /></th><th colspan="3">Jails</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
		for(n=0,nl=data.length;n<nl;n++)
		{
			var itemId='';
			if(typeof data[n]['id']!='undefined') itemId=' id-'+data[n]['id'];
			html+='<tr class="link hover'+itemId+'">';
			html+=this.makeTableJailsItem(data,n);
			html+='</tr>';
		}
		html+='</tbody>';
		
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
				//this.taskJailAdd(txt_status,data[n].id,data[n].task_status,data[n].task_id);
				this.tasks.add({'operation':data[n].task_cmd,'jail_id':data[n].id,'status':data[n].task_status,'task_id':data[n].task_id});
//				this.tasks.start();
				in_progress=true;
			}
		}
		//if(data[n].operation=='start') txt_status='Launched';	//
		html+='</td><td class="jstatus">'+txt_status+'</td><td class="ops"><span class="icon-cnt"><span class="icon-'+icon+'"></span></span></td>';
		
		return html;
	},
	
	makeTableModules:function(data)
	{
	this.log_write('makeTableModules');
		var table=$('table.tbl-cnt');
		$(table).addClass('modules');
		var html='<thead><tr><th colspan="2">&nbsp;</th><th>Modules</th><th>&nbsp;</th></tr></thead><tbody>';
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
		html+='</tbody>';
		return html;
	},
	
	makeTableServices:function(data)
	{
	this.log_write('makeTableServices');
		var table=$('table.tbl-cnt');
		$(table).addClass('services');
		var html='<thead><tr><th colspan="2">&nbsp;</th><th>Services</th><th>Autostart</th><th colspan="2">&nbsp;</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>';
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
		html+='</tbody>';
		return html;
	},
	
	makeTableUsers:function(data)
	{
	this.log_write('makeTableUsers');
		var table=$('table.tbl-cnt');
		$(table).addClass('users');
		var html='<thead><tr><th colspan="2">&nbsp;</th><th>Users</th><th>&nbsp;</th></tr></thead><tbody>';
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
		html+='</tbody>';
		return html;
	},

	makeTableTaskLog:function(data)
	{
	this.log_write('makeTableTaskLog');
		var table=$('table.tbl-cnt');
		$(table).addClass('tasklog');
		var html='<thead><tr><th>Id</th><th>Command</th><th>Start time</th><th>End time</th><th>Status</th><th>Error code</th><th>Log file</th><th>Log size</th></tr></thead><tbody>';
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
			html+='<tr class="link"><td>'+data[n].id+'</td>';
			html+='<td class="text-center"><small>'+data[n].cmd+'</small></td><td class="text-center"><small>'+data[n].st_time+'</small></td><td class="text-center"><small>'+data[n].end_time+'</small></td><td class="text-center"><small>'+data[n].status+'</small></td>';
			html+='<td class="text-center"><small>'+data[n].errcode+'</small></td><td><small>'+data[n].logfile+'</small></td><td>'+data[n].filesize+'</td>';
			html+='</tr>';
		}
		html+='</tbody>';
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
			
		}

		this.selItem(tr);
		
		var cl=tr[0].className;
		if(!$(tr).hasClass('link')) return;
		var rx=new RegExp(/id-(\d+)/);
		if(res=cl.match(rx))
		{
			var id=res[1];
		}
	//debugger;
		switch(td.className)
		{
			case 'ops':
				this.jailStart(tr);
				return;break;
			case 'sett':
				this.lastJailId=id;
				this.editMode='edit';
				this.getJailSettings(tr);
				return;break;
			case 'jstatus':
				return;break;
			case 'info':
				//alert('show info about jail!');
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
				var lid=$('tr td:first-child').html();
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/log-'+lid;
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
		var rxl=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/log/);
		var rxli=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/log-(\d+)/);
		var rxu=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/users/);
		var rxs=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/services/);
		var rxm=new RegExp(/^#prj-(\d+)\/jail-(\d+)\/module-(\d+)/);
		var rxj=new RegExp(/^#prj-(\d+)\/jail-(\d+)/);
		var rxp=new RegExp(/^#prj-(\d+)\/?$/);
		if(res=hash.match(rxli)){
			// we are in log
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.log_id=parseInt(res[3]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='log';
			this.openTaskLogItem();
		}else if(res=hash.match(rxl)){
			// we are in log
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='log';
			this.openTaskLog();
		}else if(res=hash.match(rxu)){
			// we are in users
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='users';
			this.openUsers();
		}else if(res=hash.match(rxs)){
			// we are in service
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='services';
			this.openServices();
		}else if(res=hash.match(rxm)){
			// we are in module
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.module=parseInt(res[3]);
			this.states={'project':this.project,'jail':this.jail,'module':this.module};
			this.currentPage='module';
			this.openModule();
		}else if(res=hash.match(rxj)){
			// we are in jail
			this.project=parseInt(res[1]);
			this.jail=parseInt(res[2]);
			this.states={'project':this.project,'jail':this.jail};
			this.currentPage='modules';
			//this.showModulesList();
			this.openJail();
		}else if(res=hash.match(rxp)){
			// we are in project
			this.project=parseInt(res[1]);
			this.states={'project':this.project,'jail':''};
			this.currentPage='jails';
			this.openProject();
		}else{
			// we are in project list
			this.currentPage='project';
			this.openProjectsList();
		}
		this.windowClose();
		
		if(this.project>0&&this.jail>0)
		{
			$('#log-menu').attr('href','/#prj-'+this.project+'/jail-'+this.jail+'/log').show();
			$('#users-menu').attr('href','/#prj-'+this.project+'/jail-'+this.jail+'/users').show();
			$('#service-menu').attr('href','/#prj-'+this.project+'/jail-'+this.jail+'/services').show();
			$('#modules-menu').attr('href','/#prj-'+this.project+'/jail-'+this.jail).show();
		}else{
			$('#log-menu').hide();
			$('#users-menu').hide();
			$('#service-menu').hide();
			$('#modules-menu').hide();
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
			if(this.currentPage=='modules')
			{
				this.modulesOps('modremove',event);
				return;
			}
			var c=confirm(confirms[op]);
			if(!c) return;
		}
		
		var jails=$('.tbl-cnt.jails input[type="checkbox"]:checked');
		var jl=0,n=0;
		for(n=0,jl=jails.length;n<jl;n++)
		{
			var tr=$(jails[n]).closest('tr');
			var id=this.getJailId(tr);
			this.clearErrorMessageByJailId(id);
			var j=this.getJailById(id);
			if((j.status==1 && op!='jstop') || (j.status==0 && op=='jstop'))
			{
				this.writeErrorMessageByJailId(id,msgs[op]);
			}else{
				this.enableWait(id);
				this.tasks.add({'operation':op,'jname':j.name,'jail_id':id});
			}
		}
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
				this.loadData('addProject',$.proxy(this.onLoadData,this),posts);
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
					break;
				case 'jails':
					if(data.editMode=='edit')
					{
						this.jailEdit(data);
					}else{
						this.jailsList=data.jails;
						this.lastJailId=data.lastID;
						this.showJailsList();
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
					//alert(data.new_user);
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
			//this.tasks[id]={'operation':'jimport','jname':'jail'+id,'jail_id':id,'status':-1,'task_id':task_id};
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
		/*
		if(this.interval===null)
		{
			this.interval=setInterval($.proxy(this.getTasks,this),1000);
		}
		*/
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
	
	/*
	getTasks:function()
	{
		if(this.checkTasks) return;
		this.checkTasks=true;
		//var txtStatus='';
		
		/-*
		for(key in this.tasks)
		{
			var task=this.tasks[key];
			if(task.status<2)
			{
				//this.log_write(task.jail_id+' ('+task.status+')'+' — '+task.operation);
			}else if(task.status==2){
				if(typeof this.tasks[key].errmsg!='undefined')
					this.enablePlay(key);
				else{
					if(task.operation=='jstop')
					{
					//	this.enablePlay(key);
					//	txtStatus='Stopped';
					//	var num=this.getJailNumById(key);
					//	this.jailsList[num].status=0;
					}
					else if(task.operation=='jstart')
					{
					//	this.enableStop(key);
					//	txtStatus='Launched';
					//	var num=this.getJailNumById(key);
					//	this.jailsList[num].status=1;
					}
					else if(task.operation=='jedit')
					{
					//	this.enableStop(key);
					//	txtStatus='Saved';
					}
					else if(task.operation=='jremove')
					{
					//	this.enableRip(key);
					//	txtStatus='Removed';
					//	window.setTimeout($.proxy(this.deleteItemsOk,this,key),2000);
					}
					else if(task.operation=='jexport')
					{
					//	this.enablePlay(key);
					//	txtStatus='Exported';
					}
					else if(task.operation=='jimport')
					{
					//	this.enablePlay(key);
					//	txtStatus='Imported';
					}
					//txtStatus_=task.txt_status;
					//$('tr.id-'+key+' .jstatus').html(txtStatus_);
				}
				//delete this.tasks[key];
			}
		}
		*-/
		
		if($.isEmptyObject(this.tasks))
		{
			clearInterval(this.interval);
			this.interval=null;
			this.checkTasks=false;
			return;
		}
		
		var vars=JSON.stringify(this.tasks);
		this.loadData('getTasksStatus',$.proxy(this.getTasksOk,this),[{'name':'jsonObj','value':vars}]);
	},
	getTasksOk:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		for(key in data)
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
				if(typeof this.tasks[key].errmsg!='undefined')
				{
					this.enablePlay(key);
				}else{
					switch(data[key].operation)
					{
						case 'jstart':
							var num=this.getJailNumById(key);
							this.jailsList[num].status=1;
							this.enableStop(key);
							break;
						case 'jstop':
							var num=this.getJailNumById(key);
							this.jailsList[num].status=0;
							this.enablePlay(key);
							break;
						case 'jedit':
							this.enableStop(key);
							break;
						case 'jremove':
							this.enableRip(key);
							window.setTimeout($.proxy(this.deleteItemsOk,this,key),2000);
							break;
						case 'jexport':
							this.enablePlay(key);
							break;
						case 'jimport':
							this.enablePlay(key);
							break;
					}
				}
				delete this.tasks[key];
			}
		}
		
		this.checkTasks=false;
		if(this.interval===null)
		{
			//this.interval=setInterval($.proxy(this.context.getTasks,this.context),1000);
			this.interval=setInterval($.proxy(this.start,this),1000);
		}

	},
	*/
	
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
			$('#overlap').show();
			$('#window').show();
			this.resizeWindow();
		}
		return $('#window-content');
	},
	/*
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
				this.loadData('addProject',$.proxy(this.onLoadData,this),posts);
				break;
			case 'jails':
				this.loadData('addJail',$.proxy(this.onLoadData,this),posts);
				break;
			case 'modules':
				this.loadData('addModule',$.proxy(this.onLoadData,this),posts);
				break;
		}
		this.windowClose();
	},
	*/
	
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
		$('#overlap').show();
		$('.spinner').show();
	},
	waitScreenHide:function()
	{
		$('#overlap').hide();
		$('.spinner').hide();
	},
}



$(window).bind('resize',function(){iface.resize();});
$(document).ready(function(){iface.hashCheck();iface.addEvents();});
$(window).load(function(){iface.resize();});
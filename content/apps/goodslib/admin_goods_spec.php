<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * ECJIA 商品类型管理程序
*/
class admin_goods_spec extends ecjia_admin {
	public function __construct() {
		parent::__construct();

		RC_Loader::load_app_func('global', 'goods');
		RC_Loader::load_app_func('global', 'goodslib');
		RC_Loader::load_app_func('admin_goods', 'goods');
		
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Style::enqueue_style('chosen');
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-uniform');
		
		RC_Script::enqueue_script('goods_attribute', RC_App::apps_url('statics/js/goods_attribute.js', __FILE__) , array() , false, true);
		RC_Script::enqueue_script('adsense-bootstrap-editable-script', RC_Uri::admin_url() . '/statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js', array(), false, true);
		RC_Style::enqueue_style('adsense-bootstrap-editable-style', RC_Uri::admin_url() . '/statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css');

        RC_Script::localize_script('goods_attribute', 'js_lang', config('app-goodslib::jslang.attribute_page'));
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('商品库规格', 'goodslib'), RC_Uri::url('goodslib/admin_goods_spec/init')));
	}
	
	/**
	 * 管理界面
	 */
	public function init() {
		$this->admin_priv('goods_type');
		
		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('商品库规格', 'goodslib')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述', 'goodslib'),
			'content'	=> '<p>' . __('欢迎访问ECJia智能后台商品规格列表页面，系统中所有的商品规格都会显示在此列表中。', 'goodslib') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息：', 'goodslib') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:商品类型#.E5.95.86.E5.93.81.E7.B1.BB.E5.9E.8B.E5.88.97.E8.A1.A8" target="_blank">关于商品规格列表帮助文档</a>', 'goodslib') . '</p>'
		);
		
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$goods_type_list = get_goods_type();

		$this->assign('goods_type_list',	$goods_type_list);
		$this->assign('filter',				$goods_type_list['filter']);

		$this->assign('ur_here',          	__('商品库规格列表', 'goodslib'));
		$this->assign('action_link',      	array('text' => __('添加商品规格', 'goodslib'), 'href' => RC_Uri::url('goodslib/admin_goods_spec/add')));
		$this->assign('form_search',  		RC_Uri::url('goodslib/admin_goods_spec/init'));
		
		$this->display('goods_type_list.dwt');
	}
	
	/**
	 * 添加商品类型
	 */
	public function add() {
		$this->admin_priv('goods_type_update');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('添加商品规格', 'goodslib')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述', 'goodslib'),
			'content'	=> '<p>' . __('欢迎访问ECJia智能后台添加商品规格页面，可以在此页面添加商品规格信息。', 'goodslib') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息：', 'goodslib') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:商品类型#.E6.B7.BB.E5.8A.A0.E5.95.86.E5.93.81.E7.B1.BB.E5.9E.8B" target="_blank">关于添加商品规格帮助文档</a>', 'goodslib') . '</p>'
		);
		
		$this->assign('ur_here', __('添加商品规格', 'goodslib'));
		$this->assign('action_link', array('href'=>	RC_Uri::url('goodslib/admin_goods_spec/init'), 'text' => __('商品规格列表', 'goodslib')));
		
		$this->assign('action', 'add');
		$this->assign('goods_type', array('enabled' => 1));
		$this->assign('form_action',  RC_Uri::url('goodslib/admin_goods_spec/insert'));

		$this->display('goods_type_info.dwt');
	}
		
	public function insert() {
		$this->admin_priv('goods_type_update', ecjia::MSGTYPE_JSON);

		$goods_type['cat_name']		= RC_String::sub_str($_POST['cat_name'], 60);
		$goods_type['attr_group']	= RC_String::sub_str($_POST['attr_group'], 255);
		$goods_type['enabled']		= intval($_POST['enabled']);
		
		$count = RC_DB::table('goods_type')->where('cat_name', $goods_type['cat_name'])->where('store_id', 0)->count();
		if ($count > 0 ){
			return $this->showmessage(__('已经存在一个同名的商品规格。', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		} else {
			$cat_id = RC_DB::table('goods_type')->insertGetId($goods_type);
			if ($cat_id) {
				$links = array(array('href' => RC_Uri::url('goodslib/admin_goods_spec/init'), 'text' => __('返回商品规格列表', 'goodslib')), array('href' => RC_Uri::url('goodslib/admin_goods_spec/add'), 'text' => __('继续添加商品规格', 'goodslib')));
				return $this->showmessage(__('添加商品规格成功', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('goodslib/admin_goods_spec/edit', 'cat_id='.$cat_id), 'links' => $links));
			} else {
				return $this->showmessage(__('添加商品规格失败', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
	}
	
	/**
	 * 编辑商品类型
	 */
	public function edit() {
		$this->admin_priv('goods_type_update');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑商品规格', 'goodslib')));
		if (empty($_GET['cat_id'])) {
		    return $this->showmessage(__('参数丢失', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		$goods_type = RC_DB::table('goods_type')->where('cat_id', intval($_GET['cat_id']))->first();
		if (empty($goods_type)) {
			return $this->showmessage(__('没有找到指定的商品规格', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述', 'goodslib'),
			'content'	=> '<p>' . __('欢迎访问ECJia智能后台编辑商品规格页面，可以在此页面编辑商品规格信息。', 'goodslib') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息：', 'goodslib') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:商品类型#.E7.BC.96.E8.BE.91.E5.95.86.E5.93.81.E7.B1.BB.E5.9E.8B" target="_blank">关于编辑商品规格帮助文档</a>', 'goodslib') . '</p>'
		);
	
		$this->assign('ur_here', __('编辑商品规格', 'goodslib'));
		$this->assign('action_link', array('href'=>RC_Uri::url('goodslib/admin_goods_spec/init'), 'text' => __('商品规格列表', 'goodslib')));
		$this->assign('goods_type', $goods_type);
		$this->assign('form_action', RC_Uri::url('goodslib/admin_goods_spec/update'));
		
		$this->display('goods_type_info.dwt');
	}
	
	
	public function update() {
		$this->admin_priv('goods_type_update', ecjia::MSGTYPE_JSON);
		
		$goods_type['cat_name']		= RC_String::sub_str($_POST['cat_name'], 60);
		$goods_type['attr_group']	= RC_String::sub_str($_POST['attr_group'], 255);
		$goods_type['enabled']		= intval($_POST['enabled']);
		$cat_id						= intval($_POST['cat_id']);
		$old_groups					= get_attr_groups($cat_id);
		
		$count = RC_DB::table('goods_type')->where('cat_name', $goods_type['cat_name'])->where('cat_id', '!=', $cat_id)->count();

		if (empty($count)) {
			RC_DB::table('goods_type')->where('cat_id', $cat_id)->update($goods_type);
			/* 对比原来的分组 */
			$new_groups = explode("\n", str_replace("\r", '', $goods_type['attr_group']));  // 新的分组
			if (!empty($old_groups)) {
				foreach ($old_groups AS $key=>$val) {
					$found = array_search($val, $new_groups);
					if ($found === NULL || $found === false) {
						/* 老的分组没有在新的分组中找到 */
						update_attribute_group($cat_id, $key, 0);
					} else {
						/* 老的分组出现在新的分组中了 */
						if ($key != $found) {
							update_attribute_group($cat_id, $key, $found); // 但是分组的key变了,需要更新属性的分组
						}
					}
				}
			}
			return $this->showmessage(__('编辑商品规格成功。', 'goodslib'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('goodslib/admin_goods_spec/edit', 'cat_id='.$cat_id)));
		} else {
			return $this->showmessage(__('已经存在一个同名的商品规格。', 'goodslib'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	/**
	 * 删除商品类型
	 */
	public function remove() {
		$this->admin_priv('goods_type_delete', ecjia::MSGTYPE_JSON);
		
		$id = intval($_GET['id']);
		if(empty($id)) {
		    return $this->showmessage(__('请选择要删除的记录', 'goodslib'),  ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		}
		$name = RC_DB::table('goods_type')->where('cat_id', $id)->pluck('cat_name');
		
		if (RC_DB::table('goods_type')->where('cat_id', $id)->delete()) {
			ecjia_admin::admin_log(addslashes($name), 'remove', 'goods_type');
			/* 清除该类型下的所有属性 */
			$arr = RC_DB::table('attribute')->where('cat_id', $id)->lists('attr_id');
			if (!empty($arr)) {

				RC_DB::table('attribute')->whereIn('attr_id', $arr)->delete();
				RC_DB::table('goodslib_attr')->whereIn('attr_id', $arr)->delete();
			}
			return $this->showmessage(__('删除成功', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		} else {
			return $this->showmessage(__('删除失败', 'goodslib'),  ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('content' => __('删除失败', 'goodslib')));
		}
	}

	/**
	 * 修改商品类型名称
	 */
	public function edit_type_name() {
		$this->admin_priv('goods_type_update', ecjia::MSGTYPE_JSON);
		$type_id   = !empty($_POST['pk'])  		? intval($_POST['pk'])	: 0;
		$type_name = !empty($_POST['value']) 	? trim($_POST['value'])	: '';

		/* 检查名称是否重复 */
		if(!empty($type_name)) {
			$is_only = RC_DB::table('goods_type')->where('cat_name', $type_name)->count();
			if ($is_only == 0) {
				RC_DB::table('goods_type')->where('cat_id', $type_id)->update(array('cat_name' => $type_name));
				
				ecjia_admin::admin_log($type_name, 'edit', 'goods_type');
				return $this->showmessage(__('编辑成功', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('content' => stripslashes($type_name)));
			} else {
				return $this->showmessage(__('已经存在一个同名的商品规格。', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		} else {
			return $this->showmessage(__('商品规格名称不能为空！', 'goodslib'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	/**
	 * 切换启用状态
	 */
	public function toggle_enabled() {
		$this->admin_priv('goods_type', ecjia::MSGTYPE_JSON);
		
		$id		= intval($_POST['id']);				
		$val    = intval($_POST['val']);
		$data 	= array('enabled' => $val);
		
		RC_DB::table('goods_type')->where('cat_id', $id)->update($data);
		
		return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('content' => $val));
	}
}

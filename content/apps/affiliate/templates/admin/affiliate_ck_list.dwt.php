<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.affiliate.init();
</script>
<!-- {/block} -->

<!-- {block name="main_content"} -->
<!-- 分成管理-->
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
	</h3>
</div>
	
<div class="row-fluid">
	<form method="post" action="{$search_action}" name="search_from">
		<select name="status" class="w150">
			<option value=""	{if $smarty.get.status eq ''}	selected{/if}>{t domain="affiliate"}全部{/t}</option>
			<option value="0" 	{if $smarty.get.status eq '0'}	selected{/if}>{t domain="affiliate"}等待处理{/t}</option>
			<option value="1"	{if $smarty.get.status eq '1'}	selected{/if}>{t domain="affiliate"}已分成{/t}</option>
			<option value="2"	{if $smarty.get.status eq '2'}	selected{/if}>{t domain="affiliate"}取消分成{/t}</option>
		</select>
		<a class="btn m_l5 screen-btn">{t domain="affiliate"}筛选{/t}</a>
		
		<div class="top_right f_r" >
			<input type="text" name="order_sn" value="{$smarty.get.order_sn}" placeholder='{t domain="affiliate"}请输入订单号{/t}'>
			<input type="button" value='{t domain="affiliate"}搜索{/t}' class="btn search_order">
		</div>
	</form>
</div>

<div class="row-fluid">
	 <div class="span12"> 
		<table class="table table-hide-edit" id="list-table">
			<thead>
			  	<tr>
			  		<th class="w120">{t domain="affiliate"}订单号{/t}</th>
				    <th class="w100">{t domain="affiliate"}订单状态{/t}</th>
				    <th class="w100">{t domain="affiliate"}操作状态{/t}</th>
				    <th>{t domain="affiliate"}操作信息{/t}</th>
				    <th class="w110">{t domain="affiliate"}分成类型{/t}</th>
				    <th class="w100">{t domain="affiliate"}操作{/t}</th>
			  	</tr>
		  	</thead>
		  	<tbody>
			  	<!-- {foreach from=$logdb.item item=log} -->
			  	<tr align="center">
			  		<td>{$log.order_sn}</td>
			  		<td>{$order_stats[$log.order_status]}</td>
			  		<td>{$sch_stats[$log.is_separate]}</td>
			  		<td>{$log.info}</td>
			  		<td>{$separate_by[$log.separate_type]}</td>
			  		<td>
			  			<!-- {if $log.is_separate eq 0 && $log.separate_able eq 1 && $on eq 1} -->
			  			<a class="toggle_view" href='{url path="affiliate/admin_separate/admin_separate" args="id={$log.order_id}"}'  data-msg='{t domain="affiliate"}您确定要分成吗？{/t}' data-pjax-url='{url path="affiliate/admin_separate/init" args="page={$logdb.current_page}"}' data-val="separate">{t domain="affiliate"}分成{/t}</a>&nbsp;|&nbsp;
			  			<a class="toggle_view" href='{url path="affiliate/admin_separate/cancel" args="id={$log.order_id}"}'  data-msg='{t domain="affiliate"}您确定要取消分成吗？此操作不能撤销。{/t}' data-pjax-url='{url path="affiliate/admin_separate/init" args="page={$logdb.current_page}"}' data-val="cancel">{t domain="affiliate"}取消{/t}</a>
			  			<!-- {elseif $log.is_separate eq 1} -->
			  			<a class="toggle_view" href='{url path="affiliate/admin_separate/rollback" args="id={$log.log_id}"}'  data-msg='{t domain="affiliate"}您确定要撤销此次分成吗？{/t}' data-pjax-url='{url path="affiliate/admin_separate/init" args="page={$logdb.current_page}"}' data-val="rollback">{t domain="affiliate"}撤销{/t}</a>
			  			<!-- {else} -->
			  			-
			  			<!-- {/if} -->
			  		</td>
			  	</tr>
			  	<!-- {foreachelse} -->
				<tr><td class="dataTables_empty" colspan="6">{t domain="affiliate"}没有找到任何记录{/t}</td></tr>
            	<!-- {/foreach} -->
			</tbody>
		</table>
		<!-- {$logdb.page} -->
	</div>
</div>
<!-- {/block} -->
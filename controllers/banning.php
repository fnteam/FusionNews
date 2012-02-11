<?php

if ( !has_access (NEWS_EDITOR) )
{
	trigger_error ($lang['ind19'], E_USER_WARNING);
}

$title = 'IP Banning';
$ips = get_banned_ips();

?>
<script type="text/javascript">
//<![CDATA[
$(function()
{
    function delete_ip()
    {
        var table_row = $(this).parent().parent();
        var ip_address = table_row.attr ("id").substring (3);
        $.ajax ("?id=banning_remove&ajax=1", {
			type: "post",
			data: { ip: ip_address },
			success: function ( data, textStatus, jkXHR )
			{
				table_row.remove();
			},
			error: function ( jqXHR, textStatus, errorThrown )
			{
				$("#message").html ("IP address entered is not valid.");
			}
        });
        return false;
    }

	$("#ban_ip").submit (function()
	{
		var ip_address = $("#ip").val();
		$.ajax ("?id=banning_add&ajax=1", {
			type: "post",
			data: { ip: ip_address },
			success: function ( data, textStatus, jkXHR )
			{
				var table = document.getElementById ('ip_address_table');
				var row = table.insertRow (-1);
                row.id = "ip-" + ip_address;
				var leftCell = row.insertCell (0);
				var rightCell = row.insertCell (1);
				
				leftCell.innerHTML = '<a href="http://www.db.ripe.net/whois?searchtext=' + ip_address + '">' + ip_address + '</a>';
                
                var link = document.createElement ('a');
                link.href = '?id=banning_remove&amp;ip=' + ip_address;
                link.innerHTML = 'Delete';
                
                $(link).click (delete_ip);
				rightCell.appendChild (link);

				$("#ip").val ("");
			},
			error: function ( jqXHR, textStatus, errorThrown )
			{
				$("#message").html ("IP address entered is not valid.");
			}
		});
		return false;
	});
    
    $(".delete-ip").click (delete_ip);
});
//]]>
</script>
<p>To prevent users from commenting on your posts, you can ban them by IP address. An entire class of IP address can be banned by using a wildcard (*), for example: 184.154.5.*</p>
<form method="post" id="ban_ip" action="?id=banning_add">
<input type="text" id="ip" name="ip" size="17" /> <input type="submit" value="Ban IP Address" /> <span id="message">&nbsp;</span>
</form>
<table class="adminpanel" id="ip_address_table">
	<thead>
		<tr>
			<th>IP Address</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
<?php if ( count ($ips) == 0 ): ?>
		<tr>
			<td colspan="2">No IP addresses are currently banned.</td>
		</tr>
<?php else: ?>
	<?php foreach ( $ips as $ip ): ?>
		<tr id="ip-<?php echo $ip ?>">
			<td><a href="http://www.db.ripe.net/whois?searchtext=<?php echo $ip; ?>"><?php echo $ip; ?></a></td>
			<td><a href="?id=banning_remove" class="delete-ip">Delete</a></td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
	</tbody>
</table>
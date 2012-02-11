<table style="border-collapse:collapse; border: 1px solid #DCDCDC; width: 100%">
    <tr style="background-color:whitesmoke">
        <td>
            {subject} :: {date} :: posted by <b>{user}</b>
        </td>
    </tr>
    <tr>
        <td>
            {news}
            {fullstory}
        </td>
    </tr>
    <tr style="background-color:whitesmoke">
        <th>Comments</th>
    </tr>
    {comments}
    <tr style="background-color:whitesmoke">
        <th style="font-weight:normal">{prev_page|<<< Prev} {pagination} {next_page|Next >>>}</th>
    </tr>
</table>
<table style="border-collapse:collapse; border: 1px solid #DCDCDC; width: 100%">
    <thead>
        <tr style="background-color:whitesmoke">
            <th>Add Comment</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                [form]
                <table style="border-collapse:collapse; width:100%">
                    <tfoot>
                        <tr style="background-color:whitesmoke">
                            <td colspan="2" style="text-align:center">
                                [buttons]
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr>
                            <td style="width:40%; text-align:right">Name</td>
                            <td>[namefld,20]</td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">Email</td>
                            <td>[mailfld,20] (optional)</td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">Remember me</td>
                            <td>[rememberchk]</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center">
                                :: Comment ::<br />
                                [comfld,60,10]
                            </td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">Characters Left</td>
                            <td>[comlen]</td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">&nbsp;</td>
                            <td>[securityimg]</td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">Security Code</td>
                            <td align="left">[securityfld] (copy the security code from the image above)</td>
                        </tr>
                        <tr>
                            <td style="width:40%; text-align:right">Password</td>
                            <td align="left">[pwfld,20] (admins only)</td>
                        </tr>
                    </tbody>
                </table>
                [/form]
            </td>
        </tr>
    </tbody>
</table>
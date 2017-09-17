<?php
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
if (!User::isAdmin()) {
    header("Location: {$global['webSiteRootURL']}?error=" . __("You can not manager plugins"));
    exit;
}
require_once $global['systemRootPath'] . 'objects/plugin.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?>  :: <?php echo __("Plugins"); ?></title>

        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
    </head>

    <body>
        <?php
        include 'include/navbar.php';
        ?>

        <div class="container">

            <table id="grid" class="table table-condensed table-hover table-striped">
                <thead>
                    <tr>
                        <th data-column-id="name" data-formatter="name" ><?php echo __("Name"); ?></th>
                        <th data-column-id="description"><?php echo __("description"); ?></th>
                        <th data-column-id="commands" data-formatter="commands" data-sortable="false"></th>
                    </tr>
                </thead>
            </table>
            <div id="pluginsFormModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><?php echo __("Plugin Form"); ?></h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-compact"  id="updatePluginForm" onsubmit="">
                                <input type="hidden" id="inputPluginId"  >
                                <label for="inputData" class="sr-only">Object Data</label>
                                <textarea class="form-control" id="inputData"  rows="5"  placeholder="Object Data"></textarea>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __("Close"); ?></button>
                            <button type="button" class="btn btn-primary" id="savePluginBtn"><?php echo __("Save changes"); ?></button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!--/.container-->
        <?php
        include 'include/footer.php';
        ?>
        <script>
            $(document).ready(function () {
                var grid = $("#grid").bootgrid({
                    ajax: true,
                    url: "<?php echo $global['webSiteRootURL'] . "pluginsAvailable.json"; ?>",
                    formatters: {
                        "commands": function (column, row) {
                            var editBtn = '';
                            if (row.id) {
                                editBtn = '<button type="button" class="btn btn-xs btn-default command-edit" data-row-id="' + row.id + '" data-toggle="tooltip" data-placement="left" title="Edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>';
                            }
                            var checked = "";
                            if (row.enabled) {
                                checked = " checked='checked' ";
                            }
                            var sqlBtn = '';
                            if(row.databaseScript){
                                sqlBtn = '<button type="button" class="btn btn-xs btn-default command-sql" data-row-id="' + row.id + '" data-toggle="tooltip" data-placement="left" title="Run Database Script"><span class="fa fa-database" aria-hidden="true"></span></button>';
                            }
                            var switchBtn = '<div class="material-switch pull-right"><input name="enable' + row.uuid + '" id="enable' + row.uuid + '" type="checkbox" value="0" class="pluginSwitch" ' + checked + ' /><label for="enable' + row.uuid + '" class="label-success"></label></div>';
                            return editBtn + sqlBtn + switchBtn;
                        },
                        "name": function (column, row) {
                            var txt = row.name + " (" + row.dir + ")<br><small class='text-muted'>UUID: " + row.uuid + "</small>";
                            return txt;
                        }
                    }
                }).on("loaded.rs.jquery.bootgrid", function () {
                    /* Executes after data is loaded and rendered */
                    grid.find(".pluginSwitch").on("change", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        console.log(row);
                        modal.showPleaseWait();
                        $.ajax({
                            url: 'switchPlugin',
                            data: {"uuid": row.uuid, "name": row.name, "dir": row.dir, "enable": $('#enable' + row.uuid).is(":checked")},
                            type: 'post',
                            success: function (response) {
                                modal.hidePleaseWait();
                                $("#grid").bootgrid('reload');
                            }
                        });
                    });

                    grid.find(".command-edit").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        console.log(row);
                        $('#inputPluginId').val(row.id);
                        $('#inputData').val(JSON.stringify(row.data_object));

                        $('#pluginsFormModal').modal();
                    });
                                        
                    grid.find(".command-sql").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        console.log(row);
                        $('#inputPluginId').val(row.id);
                        $('#inputData').val(JSON.stringify(row.data_object));
                        modal.showPleaseWait();
                        $.ajax({
                            url: 'runDBScriptPlugin.json',
                            data: {"name": row.name},
                            type: 'post',
                            success: function (response) {
                                modal.hidePleaseWait();
                            }
                        });
                    });
                });


                $('#savePluginBtn').click(function (evt) {
                    modal.showPleaseWait();
                    $.ajax({
                        url: 'addDataObjectPlugin.json',
                        data: {"id": $('#inputPluginId').val(), "object_data":  $('#inputData').val()},
                        type: 'post',
                        success: function (response) {
                            modal.hidePleaseWait();
                            $("#grid").bootgrid('reload');
                            $('#pluginsFormModal').modal('hide');
                        }
                    });
                });
            });

        </script>
    </body>
</html>

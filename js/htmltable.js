var htmltable_titleinput = '<input type="text" placeholder="Title" tabindex="1" />';
var htmltable_datainput = '<input type="text" placeholder="Data"  tabindex="1" />';
var htmltable_removecol_button = '<a href="#" class="htmltable_removecol_button" title="Remove Column"><button><i class="icon-minus"></i></button></a><br/>';

var htmltable_cols = currentTable ? currentTable[0].length : 2;
var htmltable_initial_rows = currentTable ? currentTable.length - 1 : 1;

function htmltable_addcol() {
    ++htmltable_cols;

    $('#htmltable_edittable tr:not(".addrow")').each(function() {
        if ($(this).hasClass('header')) {
            $(this).children('th').last().before('<th>' + htmltable_removecol_button + htmltable_titleinput + '</th>');
        } else {
            $(this).children('td').last().before('<td>' + htmltable_datainput + '</td>');
        }
    });

    return false;
}


function htmltable_addheader() {
    var tr = '<tr class="header">';
    for (var i = 0; i < htmltable_cols; ++i) {
        tr += '<th style="height:70px;">';
        if (i >= 2) {
            tr += htmltable_removecol_button;
        }
        tr += htmltable_titleinput;
        tr += '</th>';
    }
    tr += '<th class="addcol" style="width:32px;"><a href="#" class="htmltable_addcol_button" title="Add A Column"><button><i class="icon-plus"></i></button></a></th>';
    tr += '</tr>';

    $('#htmltable_edittable tr.addrow').before(tr);

    return false;
}

function htmltable_addrow() {
    var tr = '<tr class="data">';

        for (var i = 0; i < htmltable_cols; ++i) {
            if (i === 0) {
                tr += '<td><a href="#" class="htmltable_removerow_button" title="Remove Row"><button><i class="icon-minus"></i></button></a> ' + htmltable_datainput + '</td>';
            } else {
                tr += '<td>' + htmltable_datainput + '</td>';
            }
        }

        tr += '<td style="width:32px;">&nbsp;</td>';

    tr += '</tr>';

    $('#htmltable_edittable tr.addrow').before(tr);

    return false;
}

$(document).on('click', '.htmltable_addcol_button', htmltable_addcol);
$(document).on('click', '.htmltable_addrow_button', htmltable_addrow);

// Remove row
$(document).on('click', '.htmltable_removerow_button', function() {
    $(this).closest('tr').remove();
    return false;
});

// Remove column
$(document).on('click', '.htmltable_removecol_button', function() {
    var index = $(this).closest('th').index() + 1;

    $('#htmltable_edittable tr').each(function() {
        $(this).children('th:nth-child(' + index + '), td:nth-child(' + index + ')').remove();
    });

    return false;
});

// Add htmltable_initial_rows rows by default
htmltable_addheader();
for (var i = 0; i < htmltable_initial_rows; ++i) {
    htmltable_addrow();
}

function htmltable_export() {
    var table = [];
    $('#htmltable_edittable tr:not(.addrow)').each(function() {
        var row = [];
        $(this).find('input').each(function() {
            row.push($(this).val());
        });
        table.push(row);
    });
    return table;
}

// When submitting the form, put the table data into a field
$(document).on('submit', '#mform1', function() {
    var table = htmltable_export();
    table = JSON.stringify(table);
    $('input[name=content]').val(table);
});

// Populate table with existing content
if (currentTable) {
    $('#htmltable_edittable tr:not(.addrow)').each(function(rowNum) {
        var row = currentTable[rowNum];

        $(this).find('th input, td input').each(function(colNum) {
            $(this).val(row[colNum]);
        });
    });
}

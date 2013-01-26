var videoFormTemplate = null;

function createInputTagsBind(parent)
{
    $(parent).find('.pornstar_input').tagsInput({
        'defaultText'       : 'pornstars...',
        'autocomplete_url'  : autocompletePornstarsUrl,
        'autocomplete'      : {
                                selectFirst     :true,
                                width           :'100px',
                                autoFill        :true,
                                minLength       :2,
                                focus           :function(event, ui) {
                                                    img = $(this).parentsUntil("#clip_form").find(".pornstar-image");

                                                    if (ui.item.imgsrc == "" || ui.item.imgsrc == null) {
                                                        $(img).attr("src", pornstarPlaceholder);
                                                    } else {
                                                        $(img).attr("src", ui.item.imgsrc);
                                                    }

                                                    $(img).show();
                                },
                                close           :function () {
                                                    img = $(this).parentsUntil("#clip_form").find(".pornstar-image");
                                                    $(img).hide();
                                                 }
                              },
        'height'            : '18px',
        'width'             : '400px',
        'allowNew'          : false
    });

    $(parent).find('.tags_input').tagsInput({
        'defaultText'       :'tags...',
        'height'            :'18px',
        'autocomplete_url'  : autocompleteTagsUrl,
        'autocomplete'      : {selectFirst:true, width:'100px', autoFill:true, minLength:2},
        'width'             :'400px',
        'allowNew'          : false
    });
}

function createBinds()
{
    $('.remove_current_clip').unbind('click');
    $('.remove_current_clip').on("click", function () {
        if ($("#cuttingClipForm").find('.clip_form').length == 1) {
            alert("Error: there are only one clip");
            return;
        }

        var element = this;
        bootbox.confirm("Are you sure you want to remove clip?", function(result) {
            if (result){
                $(element).parentsUntil(".clip_form").parent().remove();
            }
        });
    });

    $('.add_new_clip').unbind('click');
    $('.add_new_clip').on("click", function () {
        form = videoFormTemplate.clone().find(':input').each(function(){
            $(this).val("");
        }).end().prependTo('#clipForm');
        createBinds();
        createInputTagsBind(form);
    });

    $('.select_entire_video_button').unbind('click');
    $('.select_entire_video_button').on("click", function () {
        destination = $(this).data('dest');
        start = 0;
        end = videoDuration;

        $(this).parentsUntil(".clip_form").find("input[name=\""+destination+"[start][]"+"\"]").val(secondsToTime(start));
        $(this).parentsUntil(".clip_form").find("input[name=\""+destination+"[end][]"+"\"]").val(secondsToTime(end));
    });

    $('#submitButton').unbind('click');
    $('#submitButton').on("click", function () {
        if ($("#cuttingClipForm").find('.clip_form').length == 0) {
            alert("Error: there are no clips");
            return;
        }
        var hasErrors = false;
        $("#cuttingClipForm").find(':input').not(':button').each(function(){
            if (this.name != "") {
                if ($(this).val() == "") {

                    if (this.name != 'clip[pornstars][]') {
                        hasErrors = true;
                    }

                    if (this.name == 'clip[tags][]') {
                        $(this).parentsUntil(".clip_form").find(".tags-error-message").html('Clip require at least 1 tag');
                        $('#' + this.id + '_tagsinput').addClass('error')
                    } else {
                        $(this).addClass('error');
                    }
                } else {
                    if (this.name == 'clip[pornstars][]') {
                        invalidPornstars = validatePornstars($(this).val());
                        if (invalidPornstars.length == 0) {
                            $('#' + this.id + '_tagsinput').removeClass('error')
                            $(this).parentsUntil(".clip_form").find(".pornstars-error-message").html('');
                        } else {
                            hasErrors = true;
                            $('#' + this.id + '_tagsinput').addClass('error');

                            str = 'Invalid pornstars: ';
                            for (var i=0; i<invalidPornstars.length; i++) {
                                str = str + invalidPornstars[i] + ", ";
                            }
                            $(this).parentsUntil(".clip_form").find(".pornstars-error-message").html(str);
                        }
                    } else if (this.name == 'clip[tags][]') {
                        invalidTags = validateTags($(this).val());
                        if (invalidTags.length == 0) {
                            $('#' + this.id + '_tagsinput').removeClass('error')
                            $(this).parentsUntil(".clip_form").find(".tags-error-message").html('');
                        } else {
                            hasErrors = true;
                            $('#' + this.id + '_tagsinput').addClass('error');

                            str = 'Invalid tags: ';
                            for (var i=0; i<invalidTags.length; i++) {
                                str = str + invalidTags[i] + ", ";
                            }
                            $(this).parentsUntil(".clip_form").find(".tags-error-message").html(str);
                        }
                    } else {
                        $(this).removeClass('error');
                    }
                }
            }
        }).end();

        $("#cuttingClipForm").find('.clip_form').each(function(){
            var startTime = $(this).find('input[name="clip[start][]"]').val();
            var endTime = $(this).find('input[name="clip[end][]"]').val();

            if (!validateStrTimes(startTime, endTime)) {
                $(this).find('input[name="clip[end][]"]').addClass('error');
                hasErrors = true;
            }
        }).end();

        if (!hasErrors) {
            mixpanel.identify(userName);
            if(videoDuration > 0){
                videoDuration = Math.round(videoDuration/60);
            }
            mixpanel.track('video_cut',
                {
                    'contentManager': userName,
                    'duration': videoDuration
                }
            );

            $('#cuttingClipForm .clip_form').each(function(){
                var startTime = $(this).find('input[name="clip[start][]"]').val();
                var endTime = $(this).find('input[name="clip[end][]"]').val();

                var duration = timeToMinutes(endTime)-timeToMinutes(startTime);
                mixpanel.track('clip_created',
                    {
                        'contentManager': userName,
                        'duration': Math.round(duration)
                    }
                );
            });

            // mixpanel track_forms (is the ideal) doesn't allow submit from js nor tracking various events on submit.
            // we put the timeout to make sure it arrived
            setTimeout(
                function(){document.getElementById("cuttingClipForm").submit()},
                3000
            );

        }
    });
}

$(document).ready(function () {
    videoFormTemplate = $('#clipForm .clip_form:first').clone();

    createBinds();
    createInputTagsBind($('body,html'));

    $('#cancel_cutting').on("click", function () {
        bootbox.confirm("Are you sure you want to cancel?", function(result) {
            if (result){
                window.location.href = cancelUrl;
            }
        });
    });

    $('#mark_as_unsuitable').on("click", function () {
        bootbox.confirm("Are you sure you want to mark this video as unsuitable?", function(result) {
            if (result){
                window.location.href = unsuitableUrl;
            }
        });
    });
});

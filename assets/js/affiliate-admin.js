/**
 * Affiliate Link Admin JavaScript
 *
 * @package EarnForex_WP
 * @since 1.0.5
 */

(function($) {
    'use strict';

    var efpAff = window.efpAff || {};

    // 初始化
    $(document).ready(function() {
        initSortable();
        initStatusToggle();
        initOrderInput();
        initBulkActions();
        initPreview();
    });

    /**
     * 可拖拽排序
     */
    function initSortable() {
        var $tbody = $('#the-list');
        if (!$tbody.length) return;

        $tbody.sortable({
            items: 'tr:not(.inline-edit-row)',
            handle: '.handlediv',
            cursor: 'move',
            axis: 'y',
            containment: 'parent',
            tolerance: 'pointer',
            helper: 'clone',
            opacity: 0.8,
            start: function(e, ui) {
                ui.helper.css('width', '100%');
                ui.placeholder.height(ui.helper.outerHeight());
            },
            update: function(e, ui) {
                var order = [];
                $tbody.find('tr[post-id]').each(function() {
                    order.push($(this).attr('post-id'));
                });
                saveSortOrder(order);
            }
        });
    }

    function saveSortOrder(order) {
        $.post(efpAff.ajaxurl, {
            action: 'efp_aff_sort',
            nonce: efpAff.nonce,
            order: order
        }, function(response) {
            if (response.success) {
                showNotice(response.data.message, 'success');
            } else {
                showNotice(response.data.message || 'Sort failed', 'error');
            }
        }).fail(function() {
            showNotice('Network error', 'error');
        });
    }

    /**
     * 状态切换
     */
    function initStatusToggle() {
        $(document).on('click', '.efp-status-badge', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $badge = $(this);
            var postId = $badge.data('id');
            var currentStatus = $badge.data('status');
            var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

            $badge.addClass('updating');

            $.post(efpAff.ajaxurl, {
                action: 'efp_aff_toggle_status',
                nonce: efpAff.nonce,
                id: postId,
                status: newStatus
            }, function(response) {
                $badge.removeClass('updating');
                if (response.success) {
                    $badge.data('status', newStatus);
                    $badge.removeClass('efp-status-active efp-status-inactive')
                        .addClass(newStatus === 'active' ? 'efp-status-active' : 'efp-status-inactive')
                        .text(newStatus === 'active' ? efpAff.strings.active : efpAff.strings.inactive);
                    showNotice('Status updated', 'success');
                } else {
                    showNotice(response.data.message || 'Failed', 'error');
                }
            }).fail(function() {
                $badge.removeClass('updating');
                showNotice('Network error', 'error');
            });
        });
    }

    /**
     * 排序输入框
     */
    function initOrderInput() {
        var timeout;
        $(document).on('change', '.efp-order-input', function() {
            var $input = $(this);
            var postId = $input.data('id');
            var order = parseInt($input.val()) || 0;

            clearTimeout(timeout);
            timeout = setTimeout(function() {
                $input.addClass('saving');

                // 更新本地显示顺序（可选：发送到服务器）
                // 这里可以添加 AJAX 保存排序
                $input.removeClass('saving');
            }, 300);
        });
    }

    /**
     * 批量操作
     */
    function initBulkActions() {
        $('#doaction, #doaction2').on('click', function(e) {
            var $btn = $(this);
            var action = $btn.siblings('select[name="action"]').val();
            var $checkboxes = $('#the-list input[type="checkbox"]:checked');

            if (!action || action === '-1') {
                return;
            }

            if (!$checkboxes.length) {
                alert(efpAff.strings.noItemsSelected);
                e.preventDefault();
                return;
            }

            if (!confirm(efpAff.strings.confirmBulk)) {
                e.preventDefault();
                return;
            }

            var ids = [];
            $checkboxes.each(function() {
                ids.push($(this).val());
            });

            e.preventDefault();

            $.post(efpAff.ajaxurl, {
                action: 'efp_aff_bulk_action',
                nonce: efpAff.nonce,
                bulk_action: action,
                ids: ids
            }, function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    showNotice(response.data.message || 'Failed', 'error');
                }
            }).fail(function() {
                showNotice('Network error', 'error');
            });
        });
    }

    /**
     * 实时预览
     */
    function initPreview() {
        var $targetUrl = $('#efp_aff_target_url');
        var $slug = $('#efp_aff_slug');

        if ($targetUrl.length) {
            $targetUrl.on('input blur', function() {
                updatePreview();
            });
        }

        if ($slug.length) {
            $slug.on('input blur', function() {
                updatePreview();
            });
        }

        function updatePreview() {
            var url = $targetUrl.val() || '';
            var slug = $slug.val() || '';
            var $preview = $('#efp-aff-preview');

            if (!$preview.length) return;

            var previewUrl = url;
            var redirectUrl = '';

            if (slug) {
                redirectUrl = window.location.origin + '/go/' + slug + '/';
            }

            var html = '';
            if (url) {
                html += '<strong>Target URL:</strong> ' + url + '<br>';
            }
            if (redirectUrl) {
                html += '<strong>Redirect URL:</strong> <a href="' + redirectUrl + '" target="_blank">' + redirectUrl + '</a>';
            }

            $preview.html(html || 'Fill in the fields above to see preview');
        }
    }

    /**
     * 显示通知
     */
    function showNotice(message, type) {
        type = type || 'info';

        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);

        setTimeout(function() {
            $notice.fadeOut(function() { $(this).remove(); });
        }, 5000);
    }

    // 字符串本地化
    if (typeof efpAff.strings === 'undefined') {
        efpAff.strings = {
            active: 'Active',
            inactive: 'Inactive',
            noItemsSelected: 'No items selected',
            confirmBulk: 'Are you sure you want to perform this action on selected items?'
        };
    }

})(jQuery);
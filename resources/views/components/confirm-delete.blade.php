{{-- Global delete confirmation dialog --}}
<div class="modal fade" id="deleteConfirmDialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="deleteConfirmTitle">Are you sure?</h5>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmMessage" class="mb-0">Do you want to delete this item?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteConfirmButton">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dialog = document.getElementById('deleteConfirmDialog');
    const title = document.getElementById('deleteConfirmTitle');
    const message = document.getElementById('deleteConfirmMessage');
    const deleteButton = document.getElementById('deleteConfirmButton');

    if (!dialog || !deleteButton) {
        return;
    }

    let pendingForm = null;
    let confirmedForm = null;
    let modal = null;

    function getModal() {
        if (!modal) {
            modal = new bootstrap.Modal(dialog);
        }

        return modal;
    }

    function openDeleteDialog(form, customMessage, customTitle) {
        pendingForm = form;
        title.textContent = customTitle || 'Are you sure?';
        message.textContent = customMessage || 'Do you want to delete this item?';
        getModal().show();
    }

    function extractConfirmMessage(form, submitter) {
        const inlineConfirm = form.getAttribute('onsubmit') || '';
        const match = inlineConfirm.match(/confirm\(['"](.+?)['"]\)/);

        return submitter?.dataset.confirmMessage || form.dataset.confirmMessage || (match ? match[1] : null);
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;
        const methodInput = form.querySelector('input[name="_method"]');
        const isDeleteForm = methodInput && methodInput.value.toUpperCase() === 'DELETE';

        if (!isDeleteForm || form.dataset.skipDeleteConfirm === 'true' || confirmedForm === form) {
            confirmedForm = null;
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        openDeleteDialog(form, extractConfirmMessage(form, event.submitter), event.submitter?.dataset.confirmTitle || form.dataset.confirmTitle);
    }, true);

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-delete-form], [data-delete-url], .delete-cart-btn');
        if (!trigger) {
            return;
        }

        const formId = trigger.dataset.deleteForm || (trigger.dataset.itemId ? `deleteCartForm${trigger.dataset.itemId}` : null);
        const form = formId ? document.getElementById(formId) : null;

        if (!form && !trigger.dataset.deleteUrl) {
            return;
        }

        event.preventDefault();

        if (form) {
            openDeleteDialog(form, trigger.dataset.confirmMessage, trigger.dataset.confirmTitle);
            return;
        }

        const dynamicForm = document.createElement('form');
        dynamicForm.method = 'POST';
        dynamicForm.action = trigger.dataset.deleteUrl;
        dynamicForm.dataset.skipDeleteConfirm = 'true';
        dynamicForm.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(dynamicForm);
        openDeleteDialog(dynamicForm, trigger.dataset.confirmMessage, trigger.dataset.confirmTitle);
    });

    deleteButton.addEventListener('click', function () {
        if (!pendingForm) {
            getModal().hide();
            return;
        }

        const form = pendingForm;
        pendingForm = null;
        confirmedForm = form;
        getModal().hide();
        form.submit();
    });

    dialog.addEventListener('hidden.bs.modal', function () {
        pendingForm = null;
    });
});
</script>

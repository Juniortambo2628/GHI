<?php
/**
 * Universal Modal Container
 * Used for AJAX-loaded forms (create/edit)
 */
?>

<!-- Universal Modal for Create/Edit -->
<div class="modal micromodal-slide" id="universalModal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container modal-large" role="dialog" aria-modal="true" aria-labelledby="universalModal-title">
            <header class="modal__header">
                <h2 class="modal__title" id="universalModal-title">Loading...</h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="universalModal-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal micromodal-slide" id="deleteModal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="deleteModal-title">
            <header class="modal__header">
                <h2 class="modal__title" id="deleteModal-title">Confirm Delete</h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="deleteModal-content">
                <p>Are you sure you want to delete this item?</p>
            </main>
            <footer class="modal__footer">
                <button class="btn btn-secondary" data-micromodal-close>Cancel</button>
                <button class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </footer>
        </div>
    </div>
</div>


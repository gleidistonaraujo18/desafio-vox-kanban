@extends('layouts.app')

@section('title', 'Kanban - Home')

@section('content')

    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Meu Kanban</h2>

            <div class="d-flex gap-2">
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAddBoard">
                    Novo Quadro
                </button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <label for="board-select" class="mb-0">Selecionar Quadro:</label>
                <select class="form-select w-auto" id="board-select"></select>
                <div id="btnCreateColumn" style="display: none">
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAddColumn">
                        Criar Categoria
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex flex-nowrap gap-3 overflow-auto pb-3" id="columns-container" style="min-height: auto;">
        </div>

        <div class="modal fade" id="modalAddBoard" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" id="form-add-board">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Quadro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="board-name" class="form-label">Nome do Quadro</label>
                            <input type="text" class="form-control" id="board-name" name="name"
                                placeholder="Ex: Produto Mobile" required maxlength="255">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalAddColumn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" id="form-add-column">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="column-name" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" id="column-name" name="name"
                                placeholder="Ex: Backlog" required maxlength="255">
                        </div>
                        <div class="text-muted small">
                            A categoria será adicionada ao quadro selecionado.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalAddTask" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="form-add-task">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="task-title" class="form-label">Título da Task</label>
                        <input type="text" class="form-control" id="task-title" name="title"
                            placeholder="Ex: Implementar endpoint" required maxlength="255">
                    </div>
                    <div class="text-muted small">
                        A task será adicionada à categoria selecionada.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadBoards();

            $('#board-select').on('change', function() {
                const boardId = $(this).val();

                if (!boardId) {
                    $('#columns-container').empty();
                    $('#btnCreateColumn').hide();
                    return;
                }

                loadColumns(boardId);
                $('#btnCreateColumn').show();
            });

            $('#form-add-board').on('submit', function(e) {
                e.preventDefault();

                const name = $('#board-name').val().trim();
                if (!name) {
                    alert('Informe o nome do quadro.');
                    return;
                }

                createBoard(name);
            });

            $('#form-add-column').on('submit', function(e) {
                e.preventDefault();

                const boardId = $('#board-select').val();
                const name = $('#column-name').val().trim();

                if (!boardId) {
                    alert('Selecione um quadro primeiro.');
                    return;
                }

                if (!name) {
                    alert('Informe o nome da categoria.');
                    return;
                }

                createColumn(boardId, name);
            });
        });

        function loadBoards(selectedBoardId = null) {
            $.ajax({
                url: "{{ route('api.boards.index') }}",
                method: 'GET',
                success: function(boards) {
                    const $select = $('#board-select');
                    const previousBoardId = selectedBoardId ?? $select.val();

                    $select.empty();
                    $('<option/>', {
                        value: '',
                        text: 'Selecione um quadro'
                    }).appendTo($select);

                    boards.forEach((board) => {
                        $('<option/>', {
                            value: board.id,
                            text: board.name
                        }).appendTo($select);
                    });

                    if (!boards.length) {
                        $('#btnCreateColumn').hide();
                        $('#columns-container').html(`
                            <div class="alert alert-info w-100 mb-0">
                                Você ainda não possui quadros.
                            </div>
                        `);
                        return;
                    }

                    const hasPreviousBoard = previousBoardId && boards.some((board) => board.id === previousBoardId);
                    const nextBoardId = hasPreviousBoard ? previousBoardId : boards[0].id;

                    $select.val(nextBoardId).trigger('change');
                },
                error: function() {
                    alert('Não foi possível carregar seus quadros.');
                }
            });
        }

        function createBoard(name) {
            $.ajax({
                url: "{{ route('api.boards.store') }}",
                method: 'POST',
                data: {
                    name,
                    _token: "{{ csrf_token() }}"
                },
                success: function(board) {
                    hideModal('modalAddBoard');
                    $('#board-name').val('');
                    loadBoards(board.id);
                },
                error: function(xhr) {
                    showValidationError(xhr, 'Não foi possível criar o quadro.');
                }
            });
        }

        function loadColumns(boardId) {
            $.ajax({
                url: `/api/boards/${boardId}/columns-with-tasks`,
                method: 'GET',
                success: function(columns) {
                    renderColumns(columns);
                },
                error: function() {
                    alert('Não foi possível carregar as categorias desse quadro.');
                }
            });
        }

        function renderColumns(columns) {
            const $container = $('#columns-container');
            const list = Array.isArray(columns) ? columns : Object.values(columns);

            $container.empty();

            if (!list.length) {
                $container.append(`
                    <div class="alert alert-info w-100 mb-0">
                        Nenhuma categoria criada ainda.
                    </div>
                `);
                return;
            }

            list.forEach((column) => {
                const tasksHtml = (column.tasks && column.tasks.length) ?
                    column.tasks.map((task) =>
                        `<div class="card mb-2 p-2 small card-task" draggable="true" data-task-id="${task.id}">${escapeHtml(task.title)}</div>`
                    ).join('') :
                    '<div class="text-muted small">Nenhuma task ainda</div>';

                $container.append(`
                    <div class="card shadow-sm flex-shrink-0 w-25" data-column-id="${column.id}">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <strong class="text-uppercase small">${escapeHtml(column.name)}</strong>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-add-task" data-column-id="${column.id}">
                                + Task
                            </button>
                        </div>
                        <div class="card-body p-2 column-tasks">
                            ${tasksHtml}
                        </div>
                    </div>
                `);
            });

            $(document).off('click', '.btn-add-task').on('click', '.btn-add-task', function(e) {
                e.preventDefault();
                const columnId = $(this).data('column-id');

                $('#form-add-task').data('column-id', columnId);
                showModal('modalAddTask');
            });

            $('.card-task').off('dragstart').on('dragstart', function(e) {
                const taskId = String($(this).data('task-id'));

                e.originalEvent.dataTransfer.setData('text/plain', taskId);
                e.originalEvent.dataTransfer.effectAllowed = 'move';

                $(this).addClass('dragging');
            }).off('dragend').on('dragend', function() {
                $(this).removeClass('dragging');
            });

            $('.column-tasks').off('dragover').on('dragover', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                $(this).addClass('drag-over');
            }).off('dragleave').on('dragleave', function() {
                $(this).removeClass('drag-over');
            }).off('drop').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');

                const taskId = e.originalEvent.dataTransfer.getData('text/plain');
                if (!taskId) {
                    return;
                }

                const toColumnId = $(this).closest('[data-column-id]').data('column-id');
                const clientY = e.originalEvent.clientY;
                const $children = $(this).children('.card-task').not('.dragging');
                let insertIndex = $children.length;

                for (let i = 0; i < $children.length; i++) {
                    const rect = $children.get(i).getBoundingClientRect();
                    const middle = rect.top + (rect.height / 2);

                    if (clientY < middle) {
                        insertIndex = i;
                        break;
                    }
                }

                const orderedTaskIds = $children
                    .map((_, el) => String($(el).data('task-id')))
                    .get();

                orderedTaskIds.splice(insertIndex, 0, String(taskId));

                $.ajax({
                    url: `/api/tasks/${taskId}/move`,
                    method: 'PATCH',
                    data: {
                        to_column_id: toColumnId,
                        ordered_task_ids: orderedTaskIds,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        const boardId = $('#board-select').val();
                        if (boardId) {
                            loadColumns(boardId);
                        }
                    },
                    error: function(xhr) {
                        showValidationError(xhr, 'Não foi possível mover a task.');
                    }
                });
            });
        }

        function createColumn(boardId, name) {
            $.ajax({
                url: `/api/boards/${boardId}/columns`,
                method: 'POST',
                data: {
                    name,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    hideModal('modalAddColumn');
                    $('#column-name').val('');
                    loadColumns(boardId);
                },
                error: function(xhr) {
                    showValidationError(xhr, 'Não foi possível criar a categoria.');
                }
            });
        }

        $(document).on('submit', '#form-add-task', function(e) {
            e.preventDefault();

            const columnId = $(this).data('column-id');
            const title = $('#task-title').val().trim();

            if (!columnId) {
                alert('Coluna inválida.');
                return;
            }

            if (!title) {
                alert('Informe o título da task.');
                return;
            }

            $.ajax({
                url: `/api/columns/${columnId}/tasks`,
                method: 'POST',
                data: {
                    title,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    hideModal('modalAddTask');
                    $('#task-title').val('');

                    const boardId = $('#board-select').val();
                    if (boardId) {
                        loadColumns(boardId);
                    }
                },
                error: function(xhr) {
                    showValidationError(xhr, 'Não foi possível criar a task.');
                }
            });
        });

        function showModal(modalId) {
            const modal = getModalInstance(modalId);
            if (!modal) {
                return false;
            }

            modal.show();
            return true;
        }

        function hideModal(modalId) {
            const modal = getModalInstance(modalId);
            if (!modal) {
                return false;
            }

            modal.hide();
            return true;
        }

        function getModalInstance(modalId) {
            const bs = window.bootstrap;
            const modalEl = document.getElementById(modalId);

            if (!bs || !bs.Modal || !modalEl) {
                return null;
            }

            return bs.Modal.getOrCreateInstance(modalEl);
        }

        function showValidationError(xhr, fallbackMessage) {
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    const msgs = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    alert(msgs);
                    return;
                }

                if (xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                    return;
                }
            }

            alert(fallbackMessage);
        }

        function escapeHtml(text) {
            return String(text)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    </script>
@endpush

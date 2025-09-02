<style>
    #toast-stack {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        pointer-events: none;
    }

    .alert.toast-card {
        width: 360px;
        max-width: 90vw;
        margin: 0;
        border-radius: .75rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
        pointer-events: auto;
        animation: toast-in .25s ease-out both;
    }

    .alert.toast-card.hide {
        animation: toast-out .2s ease-in forwards;
    }

    @keyframes toast-in {
        from {
            transform: translateX(-8px);
            opacity: 0
        }

        to {
            transform: none;
            opacity: 1
        }
    }

    @keyframes toast-out {
        from {
            opacity: 1
        }

        to {
            transform: translateX(8px);
            opacity: 0
        }
    }
</style>

<div id="toast-stack" aria-live="polite" aria-atomic="true"></div>

<script>
    (function() {
        const stack = document.getElementById('toast-stack');

        function show(type = 'info', message = '', {
            timeout = 7000
        } = {}) {
            const icons = {
                success: 'bx bx-check-shield',
                info: 'bx bx-info-circle',
                warning: 'bx bx-error',
                danger: 'bx bx-x-circle',
            };
            const icon = icons[type] || icons.info;

            const card = document.createElement('div');
            card.className = `alert alert-${type} alert-icon toast-card`;
            card.setAttribute('role', 'alert');

            card.innerHTML = `
      <div class="d-flex align-items-center">
        <div class="avatar-sm rounded bg-${type} d-flex justify-content-center align-items-center fs-18 me-2 flex-shrink-0">
          <i class="${icon} text-white"></i>
        </div>
        <div class="flex-grow-1">${message}</div>
        <button type="button" class="btn-close ms-2" aria-label="Close"></button>
      </div>
    `;

            const hide = () => {
                card.classList.add('hide');
                setTimeout(() => card.remove(), 180);
            };

            stack.appendChild(card);

            let remaining = timeout,
                timer, start;
            const startTimer = () => {
                start = Date.now();
                timer = setTimeout(hide, remaining);
            };
            const pauseTimer = () => {
                clearTimeout(timer);
                remaining -= Date.now() - start;
            };

            card.addEventListener('mouseenter', pauseTimer);
            card.addEventListener('mouseleave', startTimer);
            card.querySelector('.btn-close').addEventListener('click', hide);

            startTimer();
        }

        window.AppToast = {
            show
        };
    })();
</script>

{{-- Flash session bridge --}}
@php
    $flashes = [];
    foreach (['success', 'info', 'warning'] as $t) {
        if (session($t)) {
            $flashes[] = ['type' => $t, 'msg' => session($t)];
        }
    }
    if ($errors->any()) {
        foreach ($errors->all() as $key => $value) {
            $flashes[] = ['type' => 'danger', 'msg' => "There is a problem! $value"];
        }
    }
@endphp

@if ($flashes)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @foreach ($flashes as $f)
                AppToast.show('{{ $f['type'] }}', {!! json_encode($f['msg']) !!});
            @endforeach
        });
    </script>
@endif

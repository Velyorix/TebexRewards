@if($lastEnabled)
    <div class="card mb-4" id="tebexrewards-last-purchaser" data-url="{{ route('tebexrewards.api.widgets.last_purchaser') }}">
        <div class="card-body">
            <h2 class="h5 mb-2">{{ trans('tebexrewards::messages.last.title') }}</h2>

            <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <div class="fw-semibold" id="tr-last-line">
                        <span class="text-muted">{{ trans('tebexrewards::messages.last.loading') }}</span>
                    </div>
                    <div class="small text-muted" id="tr-last-time"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const root = document.getElementById('tebexrewards-last-purchaser');
            if (!root) return;

            const url = root.getAttribute('data-url');
            const line = document.getElementById('tr-last-line');
            const time = document.getElementById('tr-last-time');

            const showPackage = {{ $lastShowPackage ? 'true' : 'false' }};
            const showAmount = {{ $lastShowAmount ? 'true' : 'false' }};
            const timestampMode = @json($lastTimestamp);
            const animate = {{ $lastAnimation ? 'true' : 'false' }};
            const speed = {{ (int) $lastAnimationSpeed }};

            let lastTxnIso = null;

            function formatAbsolute(iso) {
                try {
                    const d = new Date(iso);
                    return d.toLocaleString();
                } catch (e) {
                    return '';
                }
            }

            function formatRelative(iso) {
                try {
                    const d = new Date(iso);
                    const diff = Math.floor((Date.now() - d.getTime()) / 1000);
                    if (diff < 60) return @json(trans('tebexrewards::messages.last.seconds_ago', ['seconds' => ':seconds'])).replace(':seconds', diff);
                    const min = Math.floor(diff / 60);
                    if (min < 60) return @json(trans('tebexrewards::messages.last.minutes_ago', ['minutes' => ':minutes'])).replace(':minutes', min);
                    const h = Math.floor(min / 60);
                    if (h < 24) return @json(trans('tebexrewards::messages.last.hours_ago', ['hours' => ':hours'])).replace(':hours', h);
                    const days = Math.floor(h / 24);
                    return @json(trans('tebexrewards::messages.last.days_ago', ['days' => ':days'])).replace(':days', days);
                } catch (e) {
                    return '';
                }
            }

            function render(data) {
                if (!data) {
                    line.innerHTML = '<span class="text-muted">' + @json(trans('tebexrewards::messages.last.empty')) + '</span>';
                    time.textContent = '';
                    return;
                }

                const player = data.player_name || '';
                const pkg = data.package_name || '';
                const amount = typeof data.amount === 'number' ? data.amount : null;
                const currency = data.currency || @json((string) setting('tebexrewards.goal.currency', '€'));

                let parts = [];
                if (player) parts.push(player);

                if (showAmount && amount !== null) {
                    parts.push(@json(trans('tebexrewards::messages.last.spent', ['amount' => ':amount'])).replace(':amount', amount.toFixed(2) + ' ' + currency));
                }

                if (showPackage && pkg) {
                    parts.push(@json(trans('tebexrewards::messages.last.on_package', ['package' => ':package'])).replace(':package', pkg));
                }

                line.textContent = parts.join(' ');

                if (data.purchase_date) {
                    time.textContent = (timestampMode === 'absolute') ? formatAbsolute(data.purchase_date) : formatRelative(data.purchase_date);
                } else {
                    time.textContent = '';
                }
            }

            async function refresh() {
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json.enabled) return;

                    const data = json.data || null;
                    const iso = data && data.purchase_date ? data.purchase_date : null;

                    const changed = iso && iso !== lastTxnIso;
                    lastTxnIso = iso;

                    if (animate && changed) {
                        root.style.transition = 'opacity ' + Math.max(100, speed) + 'ms ease-in-out';
                        root.style.opacity = '0.35';
                        setTimeout(() => {
                            render(data);
                            root.style.opacity = '1';
                        }, Math.max(100, speed));
                    } else {
                        render(data);
                    }
                } catch (e) {
                }
            }

            refresh();
            setInterval(refresh, 10000);
        })();
    </script>
@endif


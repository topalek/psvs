@php
    $formType = $formType ?? 'connection';
    $formTitle = $formTitle ?? 'Оставить заявку';
    $formSubtitle = $formSubtitle ?? null;
    $formShowAddress = $formShowAddress ?? false;
    $formShowCompany = $formShowCompany ?? false;
    $formSelectedTariff = $formSelectedTariff ?? null;
    $formButtonLabel = $formButtonLabel ?? 'Отправить заявку';
    $formAreas = $formAreas ?? \App\Models\Area::query()->published()->orderBy('sort_order')->get();
    $formMessageLabel = $formMessageLabel ?? 'Комментарий';
@endphp

<div class="card p-5 sm:p-7">
    <h2 class="text-xl font-bold tracking-tight text-brand-900">{{ $formTitle }}</h2>
    @if ($formSubtitle)
        <p class="mt-1.5 text-sm leading-6 text-slate-600">{{ $formSubtitle }}</p>
    @endif

    @if ($errors->any())
        <div role="alert" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Проверьте заполнение формы:</p>
            <ul class="mt-1.5 list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('leads.store') }}" class="mt-5 grid gap-4" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <input type="hidden" name="type" value="{{ old('type', $formType) }}">
        <input type="hidden" name="source_page" value="{{ old('source_page', url()->current()) }}">
        @if ($formSelectedTariff)
            <input type="hidden" name="tariff_id" value="{{ old('tariff_id', $formSelectedTariff->id) }}">
        @endif
        @foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $utm)
            @if (request()->filled($utm))
                <input type="hidden" name="{{ $utm }}" value="{{ request($utm) }}">
            @endif
        @endforeach

        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
            <label for="website-{{ $formType }}">Не заполняйте это поле</label>
            <input id="website-{{ $formType }}" type="text" name="website" value="" tabindex="-1" autocomplete="off">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="name-{{ $formType }}">Имя <span class="text-red-600">*</span></label>
                <input id="name-{{ $formType }}" name="name" type="text" required maxlength="120" autocomplete="name"
                       value="{{ old('name') }}" class="field-input" aria-describedby="name-error-{{ $formType }}">
                @error('name')<p id="name-error-{{ $formType }}" class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="phone-{{ $formType }}">Телефон</label>
                <input id="phone-{{ $formType }}" name="phone" type="tel" maxlength="32" autocomplete="tel"
                       value="{{ old('phone') }}" class="field-input" inputmode="tel">
                @error('phone')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="field-label" for="email-{{ $formType }}">E-mail</label>
            <input id="email-{{ $formType }}" name="email" type="email" maxlength="190" autocomplete="email"
                   value="{{ old('email') }}" class="field-input">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
            <p class="mt-1.5 text-xs text-slate-500">Укажите телефон или e-mail — как с вами связаться.</p>
        </div>

        @if ($formShowCompany)
            <div>
                <label class="field-label" for="company-{{ $formType }}">Название организации <span class="text-red-600">*</span></label>
                <input id="company-{{ $formType }}" name="company_name" type="text" maxlength="190" autocomplete="organization"
                       value="{{ old('company_name') }}" class="field-input">
                @error('company_name')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        @endif

        @if ($formShowAddress)
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="area-{{ $formType }}">Населённый пункт</label>
                    <select id="area-{{ $formType }}" name="area_id" class="field-input">
                        <option value="">Выберите населённый пункт</option>
                        @foreach ($formAreas as $area)
                            <option value="{{ $area->id }}" @selected((string) old('area_id') === (string) $area->id)>{{ $area->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="locality-{{ $formType }}">Населённый пункт (если нет в списке)</label>
                    <input id="locality-{{ $formType }}" name="locality" type="text" maxlength="120" value="{{ old('locality') }}" class="field-input">
                    @error('locality')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" for="street-{{ $formType }}">Улица</label>
                    <input id="street-{{ $formType }}" name="street" type="text" maxlength="190" value="{{ old('street') }}" class="field-input">
                    @error('street')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="field-label" for="house-{{ $formType }}">Дом</label>
                        <input id="house-{{ $formType }}" name="house" type="text" maxlength="32" value="{{ old('house') }}" class="field-input">
                        @error('house')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label" for="object-{{ $formType }}">Объект</label>
                        <select id="object-{{ $formType }}" name="object_type" class="field-input">
                            <option value="">Не указан</option>
                            <option value="apartment" @selected(old('object_type') === 'apartment')>Квартира</option>
                            <option value="house" @selected(old('object_type') === 'house')>Частный дом</option>
                            <option value="office" @selected(old('object_type') === 'office')>Офис</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <div>
            <label class="field-label" for="message-{{ $formType }}">{{ $formMessageLabel }}</label>
            <textarea id="message-{{ $formType }}" name="message" rows="3" maxlength="5000" class="field-input">{{ old('message') }}</textarea>
            @error('message')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-start gap-3 text-sm text-slate-600">
                <input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-accent-500 focus:ring-accent-400">
                <span>
                    Я принимаю <a href="{{ $consentTextUrl ?? '/personal-data-consent' }}" class="font-medium text-brand-600 underline underline-offset-2 hover:text-accent-600">условия обработки персональных данных</a>
                </span>
            </label>
            @error('consent')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="submitting">
                <span x-show="!submitting">{{ $formButtonLabel }}</span>
                <span x-show="submitting" x-cloak>Отправляем…</span>
            </button>
            <p class="text-xs leading-5 text-slate-500">
                Отправка заявки не подтверждает доступность подключения. Адрес проверит специалист.
            </p>
        </div>
    </form>
</div>

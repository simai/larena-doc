---
title: Сборка и диагностика документации
description: Как обновить, собрать и проверить сайт Larena Documentation.
tags: [docara, build, verify-static, diagnostics]
translation_key: larena.operations.docs
---

# Сборка и диагностика документации

> **Для кого:** сопровождающий документации. **Результат:** воспроизводимая
> локальная сборка. **Источник:** Docara 2.9.0 schemas и CLI. **Статус:**
> проверено в этом репозитории.

## Обычная проверка

1. Установите точные зависимости из lock-файла.
2. Запустите doctor и validation.
3. Выполните полную production-сборку.
4. Проверьте статический каталог и затем откройте эти же байты по HTTPS.

```bash
LARENA_DOC_PHP="${LARENA_DOC_PHP:-/Applications/ServBay/package/php/8.4/8.4.20/bin/php}"
LARENA_DOC_COMPOSER="${LARENA_DOC_COMPOSER:-/Applications/ServBay/package/bin/composer}"
"$LARENA_DOC_PHP" "$LARENA_DOC_COMPOSER" install
"$LARENA_DOC_PHP" "$LARENA_DOC_COMPOSER" docs:check
"$LARENA_DOC_PHP" vendor/bin/docara doctor --json
"$LARENA_DOC_PHP" vendor/bin/docara validate project --json
"$LARENA_DOC_PHP" vendor/bin/docara build production
"$LARENA_DOC_PHP" vendor/bin/docara verify-static build_production
```

Для сборки документации требуется PHP 8.4.1 или новее. Значения по умолчанию
выбирают проверенный PHP 8.4.20 из ServBay; для другого совместимого окружения
передайте собственные пути через эти две переменные. Требование PHP 8.3 в
разделе «Начало работы» относится к документируемой сборке Larena, а не к
инструментам этого сайта.

После изменения маршрута, меню, конфигурации или общей страницы всегда нужна
полная сборка. Каталог `build_production` является результатом компиляции и не
редактируется вручную.

## Изменение Specs или публичного контракта

Сначала выполните `composer docs:status`. Поля `public_changed_paths` и
`impacted_pages` показывают, какие исходники изменились и какие страницы надо
проверить. После редакторской проверки зафиксируйте решение командой
`scripts/documentation-impact.php accept`, затем примите изменившуюся пакетную
сущность штатной командой Docara `documentation accept`. Полные примеры и
правила решений находятся в `docs/maintenance.md` репозитория.

`composer docs:check` завершается ошибкой, если осталась хотя бы одна
непринятая ревизия, устарела производная source projection или Docara помечает
сущность как `new`, `changed`, `missing`, `missing_example`, `unverified` или
`orphan`.

## Проверка по HTTPS

ServBay должен обслуживать `build_production` как document root домена
`larena-doc.test`. Проверяйте именно собранные байты:

```bash
curl -fsSI https://larena-doc.test/ru/
curl -fsS https://larena-doc.test/ru/packages/storage/ >/dev/null
```

В браузере проверьте меню, хлебные крошки, оглавление, поиск, прямой вложенный
URL, светлую и тёмную тему, мобильную ширину и переход по клавиатуре.

## Обновление Docara

Сначала выполните `"$LARENA_DOC_PHP" vendor/bin/docara upgrade --check --json`.
Затем создайте
dry-run план для точной версии. Применение и rollback выполняются только по
hash плана. Не используйте `init --update` и не заменяйте Framework lock ссылкой
на ветку или `latest`.

Отдельный регламент описывает [публикацию, кеш и откат композиции](/operations/composition-publication/).

---
title: Страница входа как эталон композиции
description: Как /admin/login собирается через Layout, Framework Recipe и Smart Components.
tags: [auth, login, composition, example]
translation_key: larena.cms.login_composition
---

# Страница входа как эталон композиции

`/admin/login` — первый канонический вертикальный срез
`larena.frontend.composition.v1`.

```text
page.auth.login
└── центральная секция
    └── auth.login
        ├── heading
        ├── notices
        ├── fields
        ├── mfa
        └── actions
```

Auth поставляет смысл полей, локализованные подписи, безопасные ошибки, MFA и
сессионный сценарий. Layout разрешает пакетные артефакты и строит Framework
Recipe/Document. UI и Admin рендерят зарегистрированные Smart Components.
Blade содержит только HTML-контейнер, метаданные digest и уже собранный
серверный результат.

Статическая структура может повторно использоваться. CSRF, введённый email,
ошибка текущего запроса, MFA challenge, пароль, TOTP, recovery code и session ID
никогда не входят в общий Recipe, Document или snapshot. Они подставляются
только при обработке конкретного HTTP-запроса.

Приёмка проверяет EN/RU, светлую и тёмную тему, узкую ширину и RTL stress,
клавиатурный порядок, HTML без JavaScript, ошибочный и успешный вход, отсутствие
секретов в evidence и переход к защищённой Minimal CMS. Готовность этой страницы
не означает автоматическую миграцию других экранов.

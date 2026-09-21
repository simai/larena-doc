---
title: larena/ui
description: Реестр Smart-компонентов, разрешение представлений и frontend lock.
tags: [larena-ui, smart, component]
translation_key: larena.package.ui
---

# `larena/ui`

> **Для кого:** разработчик интерфейса. **Результат:** разрешить только
> зарегистрированный Smart и его зафиксированные ресурсы. **Источник:** package
> README, manifests и Root lock. **Статус:** developer foundation с точным
> frontend runtime lock.

UI владеет реестром Smart-компонентов, безопасным разрешением view и связью с
зафиксированным frontend artifact. Основные точки текущего среза —
`Larena\Ui\Runtime\SmartManager` и `FrontendRuntimeLock`.

- Composer: `larena/ui`.
- Владелец: Smart registry, rendering and frontend lock.
- Зрелость: developer foundation с exact runtime identity.

Пакет зависит от Core и Dataview. Тип компонента, props и ресурсы должны быть
объявлены в реестре; произвольный класс или путь шаблона не является допустимым
компонентом.

## Проверка

Root exact-release validator сравнивает UI revision с lock, а integration test
проверяет Smart render. Совпадение lock подтверждает идентичность ресурсов, а
не полноту всех интерфейсных состояний.

## Роль в композиции

UI владеет реестром допустимых компонентов, Smart backend renderers, вариантами,
именованными слотами и ресурсами. UI не хранит дерево страницы. Точная поставка
Framework проверяется по Git-ревизиям, manifest и digest; сгенерированные
артефакты вручную не редактируются.

## Маршруты между списками

UI связывает тип `larena.registered-list` с опубликованным элементом
`sf-table`, который владеет портами `selection` и `context`. В статическом
снимке остаётся только контейнер списка с именем endpoint; строки подставляются
при каждом запросе с правами пользователя. Хост списка, который является целью
маршрута, начинает с пустого состояния, превращает переданный выбор в один
серверный запрос с фильтром по идентификаторам записей и отвечает таблице через
её номер последовательности, поэтому устаревший ответ не заменяет новые строки.
Принято в UI `main` на ревизии `cb0aff0c60d468403064c81fe6ca79c86e025145` с
парой Framework `ui-2b9aa9635ad0-smart-db547bb87b6b`.

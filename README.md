# Проект тестового задания REST API сервис для "каталога товаров"

![Rocket Science](./Тестовое%20задание.md)

## Installation

1. Clone the repository

```bash
git clone https://github.com/yourusername/chat-app.git
cd chat-app
```

### Настройка на Linux

Ожидается, что установлена ОС `Ubuntu 22.04` или более новая LTS версия операционной системы

> [!NOTE]
> Ознакомиться со всеми командами `make` можно по [ссылке](./Makefile)

- Сборка и обновление контейнеров

``` sh
make init
```

- Запуск контейнеров и поднятие проекта

``` sh
make up
```

## Настройка на Windows

Запуск контейнеров и поднятие проекта

``` sh
docker compose up -d --build
```

### Запуск тестов

``` sh
make test
```
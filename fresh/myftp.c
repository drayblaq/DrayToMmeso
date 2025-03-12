/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void create_socket(one_t *afiss, char **av)
{
    struct sockaddr_in serv;

    afiss->fd = socket(AF_INET, SOCK_STREAM, 0);
    if (afiss->fd == -1) {
        write(2, "create failed", 13);
    }
    serv.sin_family = AF_INET;
    serv.sin_addr.s_addr = INADDR_ANY;
    serv.sin_port = htons(atoi(av[1]));
    if (bind(afiss->fd, (struct sockaddr*)&serv, sizeof(serv)) == -1) {
        fprintf(stderr, "bind failed\n");
        exit(EXIT_FAILURE);
    }
    if (listen(afiss->fd, SOMAXCONN) == -1) {
        fprintf(stderr, "listen failed\n");
    }
}

void appel_all(one_t *afiss, char **av)
{
    create_socket(afiss, av);
    my_ftp(afiss);
}

void add_client(one_t *afiss)
{
    for (int a = 0; a < MAX_CLIENT; a++) {
        if (afiss->tab_client_socket[a] == 0) {
            continue;
        }
        if (afiss->tab_client_socket[a] != 0)
            FD_SET(afiss->tab_client_socket[a], &afiss->socket);
        if (afiss->tab_client_socket[a] > afiss->socket1) {
            afiss->socket1 = afiss->tab_client_socket[a];
        }
    }
}

void select_socket(one_t *afiss)
{
    if (select(afiss->socket1 + 1, &afiss->socket, NULL, NULL, NULL) == -1
    && (errno != EINTR)) {
        printf("select error");
    }
}

void my_ftp(one_t *afiss)
{
    afiss->tab_client_socket = malloc(sizeof(int) * MAX_CLIENT);
    printf("port est %d\n", afiss->port);
    afiss->pasv = 0;
    afiss->usr = 0;
    while (1) {
        FD_ZERO(&afiss->socket);
        FD_SET(afiss->fd, &afiss->socket);
        afiss->socket1 = afiss->fd;
        add_client(afiss);
        select_socket(afiss);
        accept_connect(afiss);
        for (int j = 0; j < MAX_CLIENT; j++) {
            waou2(afiss, j);
        }
    }
}

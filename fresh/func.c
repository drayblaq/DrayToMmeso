/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void noop(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k],
        "200 Command okay.\r\n");
}

void waou2(one_t *afiss, int j)
{
    if (FD_ISSET(afiss->tab_client_socket[j], &afiss->socket) > 0) {
        command(afiss, j);
    }
}

void accept_connect(one_t *afiss)
{
    struct sockaddr_in client_address;
    socklen_t client_t;
    int cliento;

    if (FD_ISSET(afiss->fd, &afiss->socket) <= 0)
        return;
    client_t = sizeof(client_address);
    cliento = accept(afiss->fd, (struct sockaddr*)&client_address, &client_t);
    for (int u = 0; u < MAX_CLIENT; u++) {
        if (afiss->tab_client_socket[u] == 0) {
            afiss->tab_client_socket[u] = cliento;
            afiss->stat = 0;
            dprintf(cliento, "220 Service ready for new user.\r\n");
            break;
        }
    }
}

void eror2(one_t *afiss, char *buffer, int k)
{
    if (strncmp(buffer, "USER", 4) != 0
        && strncmp(buffer, "PASS", 4) != 0
        && strncmp(buffer, "QUIT", 4) != 0
        && strncmp(buffer, "HELP", 4) != 0
        && strncmp(buffer, "NOOP", 4) != 0
        && strncmp(buffer, "PWD", 3) != 0
        && strncmp(buffer, "CWD", 3) != 0
        && strncmp(buffer, "DELE", 4) != 0
        && strncmp(buffer, "CDUP", 4) != 0
        && strncmp(buffer, "PORT", 4) != 0
        && strncmp(buffer, "LIST", 4) != 0
        && strncmp(buffer, "PASV", 4) != 0
        && strncmp(buffer, "STOR", 4) != 0
        && strncmp(buffer, "RETR", 4) != 0
        && afiss->stat == 1) {
        dprintf(afiss->tab_client_socket[k], "500\n\r");
    }
}

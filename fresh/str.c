/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void comman(one_t *afiss, int i, char *buffer)
{
    if (strncmp(buffer, "USER ", 5) == 0) {
        command_user(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "PASS", 4) == 0) {
        command_password(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "QUIT", 4) == 0) {
        quit(afiss, i, buffer);
        return;
    }
}

int word_nbr(char *str, char *delim)
{
    int i = 0;
    int j = 0;

    while (str[i] != '\0') {
        if (str[i] == delim[0]) {
            j++;
        }
        i++;
    }
    j++;
    return j;
}

char **split(char *str, char *delim)
{
    int i = 0;
    char **words = malloc(sizeof(char *) * (word_nbr(str, delim) + 1));
    char *token = NULL;

    token = strtok(str, delim);
    while (token != NULL){
        words[i] = strdup(token);
        token = strtok(NULL, delim);
        i++;
    }
    words[i] = NULL;
    return words;
}

void quit(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k],
        "221 Service closing control connection.\r\n");
    close(afiss->tab_client_socket[k]);
    FD_CLR(afiss->tab_client_socket[k], &afiss->socket);
    afiss->tab_client_socket[k] = 0;
}

int main(int ac, char **av)
{
    one_t *afiss = malloc(sizeof(one_t));
    int d = chdir(av[2]);

    afiss->port = atoi(av[1]);
    if (ac != 3)
        return 84;
    if (d == -1)
        return 84;
    getcwd(afiss->dossier, sizeof(afiss->dossier));
    appel_all(afiss, av);
}

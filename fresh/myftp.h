/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#ifndef MY_FTP_H_
    #define MY_FTP_H_
    #define MAX_CLIENT 1000
    #define BUFFER 1024
    #define BUFFER 1024
    #define PATH_MAX 1024

    #include <stdio.h>
    #include <string.h>
    #include <sys/select.h>
    #include <dirent.h>
    #include <errno.h>
    #include <unistd.h>
    #include <fcntl.h>
    #include <sys/stat.h>
    #include <limits.h>
    #include <sys/types.h>
    #include <sys/socket.h>
    #include <netinet/in.h>
    #include <arpa/inet.h>
    #include <stdlib.h>
    #include <sys/wait.h>
    #include <sys/time.h>
    #include <stdbool.h>
    #include <asm-generic/socket.h>

typedef struct one {
    int fd;
    int stat;
    char com[50];
    char *path;
    char dossier[2000];
    int ok;
    int *tab_client_socket;
    int *user;
    int *pass;
    int usr;
    int pasv;
    struct sockaddr_in serv;
    int opt;
    int port;
    char *ip;
    int k;
    int socket1;
    char **tab;
    char **my_path;
    fd_set socket;
} one_t;

void create_socket(one_t *afiss, char **av);
void eror2(one_t *afiss, char *buffer, int k);
void create_serveur(one_t *afiss);
void appel_all(one_t *afiss, char **av);
void add_client(one_t *afiss);
void select_socket(one_t *afiss);
void my_ftp(one_t *afiss);
void accept_connect(one_t *afiss);
char *pat2(char *str);
char *pat(one_t *afiss, int p, char *str);
void command_pwd(one_t *afiss, int k, char *buffer);
void command_password(one_t *afiss, int k, char *buffer);
int main(int ac, char **av);
void command(one_t *afiss, int i);
void command_user(one_t *afiss, int k, char *buffer);
void waou(char *buffer, int j);
void waou2(one_t *afiss, int j);
bool backslash_n_check(char u);
int cmp_line(char *str);
char **my_str_to_word_array(char *str);
void command_stor(one_t *afiss, int k, char *buffer);
int word_nbr(char *str, char *delim);
char **split(char *str, char *delim);
void quit(one_t *afiss, int k, char *buffer);
void comman(one_t *afiss, int i, char *buffer);
void noop(one_t *afiss, int k, char *buffer);
void command_help(one_t *afiss, int k, char *buffer);
void comman2(one_t *afiss, int i, char *buffer);
void command_pasv(one_t *afiss, int k, char *buffer);
void command_port(one_t *afiss, int k, char *buffer);
void command_list(one_t *afiss, int k, char *buffer);
void command_cwd(one_t *afiss, int k, char *buffer);
void command3(one_t *afiss, int i, char *buffer);
void command_cdup(one_t *afiss, int k);
void command_retr(one_t *afiss, int k, char *buffer);
void comman4(one_t *afiss, int i, char *buffer);
void delete(one_t *afiss, int k, char *buffer);
void eror(one_t *afiss, char *buffer, int k);
#endif

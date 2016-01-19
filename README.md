# Yii2-User - README
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Provides various interfaces to deal with routine user management tasks.

> This extension is under heavy development and requires the use of Yii framework version 2.0.7
> This version of the framework is currently in-active development  

> This module should be considered highly unstable and it's use is discouraged until further notice (really)

## Features

This module aims to be a clean, modular and simple user-administration module which can be used for Yii 2 projects version 2.0.7 and up.
A first goal is to wrap the user management functionalities provided by the yii2-advanced template to allow easy porting to other templates, such as the yii2-basic-template.
Essentially, this module can functionally achieve the same as provided by the advanced template, with little to no changes made to the interface provided and minor changes to made layout.
 

## Installation

### Install Using Composer

### Add Module to Configuration

### Run Migrations 


## Module Configuration Options

### Global Configuration Options



### Grid Configuration Options

### CLI Configuration Options

### RBAC Integration

## Graphical User Interface (GUI)

Following actions are supported:

> user/admin/index: User Administration Dashboard

> user/account/index?id=<integer>: User Account Page


## Command Line Interface (CLI)

Following commands are supported:

> php yii user/admin/create <email:required> <username:optional>

> php yii user/admin/delete <email:required>

The module provides

## REST Interface (API)

Due before version 0.5
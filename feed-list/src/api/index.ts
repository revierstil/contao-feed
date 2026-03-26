// @ts-ignore
// @ts-ignore
import type {AxiosPromise} from "axios";
import axios from "axios";
import qs from 'qs';
// @ts-ignore
import config from "../config";
import {configStore} from "@/stores";
import {storeToRefs} from "pinia";

const apiUrl = config.api.baseUrl;

export const api = axios.create({
    baseURL: apiUrl,
    /*
      headers: {
          "Access-Control-Allow-Origin": "*",
          "Access-Control-Allow-Methods": "GET, POST, PATCH, PUT, DELETE, OPTIONS",
          "Access-Control-Allow-Headers": "Origin, Content-Type, X-Auth-Token"
      }
      */
});

api.interceptors.response.use((response) => {
    const requestToken = response.headers['x-contao-request-token'];

    if (requestToken) {
        configStore.requestToken = requestToken;
    }

    return response;
});

export function fetchFeed(page: number, filters: Record<string, string>, sorting: string | null): Promise<AxiosPromise> {

    let filterParams: Record<string, any> = [];

    if (sorting !== null && sorting.length > 0) {
        filterParams['sorting'] = sorting;
    }

    if (Object.keys(filters).length > 0) {
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                filterParams[key] = {
                    condition: 'eq',
                    x: filters[key]
                }
            }
        });
    }

    let query = qs.stringify({
        page: page,
        filter: filterParams
    }, {
        encodeValuesOnly: true,
        arrayFormat: 'indices'
    });

    query = (query.length > 0) ? '?' + query : '';

    return api.get(configStore.urls.listing + query);
}

export function createFeed(data: Object): Promise<AxiosPromise> {
    let formData = new FormData();
    formData.append('REQUEST_TOKEN', configStore.requestToken ?? '');

    Object.keys(data).forEach(key => {
        const value = data[key];

        if (value === undefined) return;

        // FormKit file input liefert ein Array von { name, file } Objekten
        if (Array.isArray(value) && value.length > 0 && value[0]?.file instanceof File) {
            value.forEach((fileObj: { name: string; file: File }) => {
                formData.append(key, fileObj.file, fileObj.name);
            });
        } else {
            formData.append(key, value);
        }
    });

    return api.post(configStore.urls.create, formData);
}

export function likeFeed(feedId: number): Promise<AxiosPromise> {
    let formData = new FormData();
    formData.append('REQUEST_TOKEN', configStore.requestToken ?? '');
    formData.append('feedId', feedId.toString())

    return api.post(configStore.urls.like, formData);
}
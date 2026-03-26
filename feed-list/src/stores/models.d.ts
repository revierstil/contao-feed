export interface Sorting {
    value: string;
    label: string;
    default: boolean;
}

export interface Option {
    value: string;
    label: string;
}

export interface Filter {
    label: string;
    options: Option[];
}

export interface Urls {
    listing: string;
    create: string;
    like: string;
}

export interface ConfigModel {
    sorting: Sorting[] | null;
    urls: Urls | null;
    options: Record<string, Option[]> | null;
    requestToken: string | null;
}

export interface ConfigState {
    sorting: Sorting[];
    urls: Urls;
    options: Record<string, Option[]>;
    initialized: boolean;
    requestToken: string | null;
}

export interface Author {
    firstname: string | null;
    lastname: string | null;
    username: string | null;
    avatar: Image | null;
}

export interface Location {
    id: number;
    title: string;
}

export interface ImageImg {
    srcset: string;
    src: string;
    width: number;
    height: number;
    hasSingleAspectRatio: boolean;
}

export interface ImagePicture {
    img: ImageImg;
    sources: unknown[];
    alt?: string;
}

export interface ArrSize {
    0: number;
    1: number;
    2: number;
    3: string;
    bits: number;
    channels: number;
    mime: string;
}

export interface Image {
    picture: ImagePicture;
    width: number;
    height: number;
    arrSize: ArrSize;
    imgSize: string;
    singleSRC: string;
    src: string;
    fullsize: boolean;
    addBefore: boolean;
    addImage: boolean;
    alt: string;
    caption: string;
    license: string;
    uuid: string;
    imageUrl: string;
    href: string;
    attributes: string;
    linkTitle: string;
    lightboxPicture: ImagePicture;
}

export interface Pagination {
    currentPage: number;
    hasNextPage: boolean;
    hasPreviousPage: boolean;
    pages: number;
    total: number;
    perPage: number;
}

export interface Feed {
    id: number;
    author: Author;
    location: Location;
    message: string;
    dateCreated: string;
    likes: number;
    image: Image | null;
}

export interface FeedsState {
    feeds: Feed[];
    pagination: Pagination | null;
    filterElements: Filter[];
    currentPage: number;
    filters: Record<string, string>;
    sorting: string | null;
    loading: boolean;
}
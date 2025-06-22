import { Inject, Injectable } from "@angular/core";
import { API_URL } from "../app/app.config";
import { HttpClient } from "@angular/common/http";
import { API_ROUTES } from "../constants/route.constants";
import { DirectoryDtoWithContent } from "../dtos/directory.dto";
import { firstValueFrom } from "rxjs";
import { SearchStorageResponse } from "../contracts/search-storage.response";

@Injectable({ providedIn: "root" })
export class StorageService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.storage}`;
  }

  public getDirectoryWithContent(directoryId: string) {
    const route = `${this.endpoint}?directoryId=${directoryId}`;
    const request = this.httpClient.get<DirectoryDtoWithContent>(route);
    return firstValueFrom(request);
  }

  public search(name: string, directoryId: string) {
    const route = `${this.endpoint}/search?name=${name}&directoryId=${directoryId}`;
    const request = this.httpClient.get<SearchStorageResponse>(route);
    return firstValueFrom(request);
  }
}

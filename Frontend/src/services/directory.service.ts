import { Inject, Injectable } from "@angular/core";
import { API_URL } from "../app/app.config";
import { HttpClient } from "@angular/common/http";
import { API_ROUTES } from "../constants/route.constants";
import { DirectoryDto } from "../dtos/directory.dto";
import { firstValueFrom } from "rxjs";

@Injectable({ providedIn: "root" })
export class DirectoryService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.directory}`;
  }

  public getDirectories() {
    const route = `${this.endpoint}`;
    const request = this.httpClient.get<DirectoryDto>(route);
    return firstValueFrom(request);
  }

  public createDirectory(name: string, parentId: string) {
    const route = `${this.endpoint}`;
    const payload = {
      name: name,
      parentId: parentId,
    } as const;
    const request = this.httpClient.post<DirectoryDto>(route, payload);
    return firstValueFrom(request);
  }
}

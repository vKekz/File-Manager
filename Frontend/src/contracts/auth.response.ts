import { UserDto } from "../dtos/user.dto";

export interface AuthResponse {
  user: UserDto;
  accessToken: string;
}
